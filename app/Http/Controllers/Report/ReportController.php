<?php

namespace App\Http\Controllers\Report;

use App\Models\Content\Post;
use App\Models\Report\Report;
use App\Traits\PostTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Content\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    use PostTrait;

    private $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }



    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = $request->all();
        $type  = $param['type'] ?? '';
        $type  = in_array($type, ['text', 'image', 'video', 'vote']) ? $type : '';

        $sql   = Report::select('reportable_id', DB::raw('count(1) as num_all'))
            ->where(['status'=>0,'reportable_type'=>"App\Models\Post"])
            ->where('created_at', '>=', '2020-11-12 05:49:00')
            ->groupBy('reportable_id')->orderByDesc('id')->having('num_all', '>', 0);

        $count  = $sql->get()->count();
        $report = $sql->first();

        if (empty($report)) {
            return view('backstage.report.index');
        }

        $post   = Post::withTrashed()->where('post_id', $report['reportable_id']);
        $result = $post->with('translations')->orderByDesc('post_created_at')->first();
        $result['totalCount']  = $count ?? 0;
        $result['reportTimes'] = $report['num_all'] ?? 0;

        if ($type=='vote') {
            $database = config('database.front_database');
            $voteInfo = DB::select("select v.tab_name, v.vote_media, v.default_locale,t.content from $database.f_vote_details v left join $database.f_vote_details_translations t on v.id=t.vote_detail_id where v.post_id=? and t.locale=?", [$result['post_id'], 'zh-CN']);
            $voteInfo = array_map(function($item){
                if(!empty($item->vote_media)) {
                    $item->vote_media = json_decode($item->vote_media, true);
                    $item->vote_media['image']['image_url'] = config('common.qnUploadDomain.thumbnail_domain').$item->vote_media['image']['image_url']. '?imageMogr2/auto-orient/interlace/1|imageslim';
                }
                return $item;
            }, $voteInfo);
            $result['vote_info'] = $voteInfo;
        }

        if (!empty($result)) {
            $result = collect($result)->toArray();

            $languages = array_unique(['en', 'zh-CN', $result['post_content_default_locale'] ?? null]);
            $result['media'] = !empty($result['post_media']) ? json_decode($result['post_media'], true) : [];

            if (!empty($result['post_media'])) {
                $result['media'] = json_decode($result['post_media'], true);
                $type            = !empty($result['media']['image']) ? 'image' : 'video';
                $result['media'] = postMedia($type, $result['media'], 3);
            }

            $result['post_default_content'] = '';
            if (!empty($result['translations'])) {
                foreach ($result['translations'] as $key=>$translation) {
                    if ($result['post_content_default_locale'] == $translation['post_locale']) {
                        $result['post_default_content'] = $translation['post_content'];
                    }
                    if (in_array($translation['post_locale'], $languages)) {
                        $result['trans'][$translation['post_locale']] = $translation['post_content'];
                    }
                }
            }
        } else {
            $result = [];
        }

        return view('backstage.report.index', compact('result'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param   = $request->all();
        $id      = $param['id'] ?? '';
        $status  = $param['status'] ?? '';
        $userId  = $param['userId'] ?? '';
        $time    = date('Y-m-d H:i:s');

        if ($id && in_array($status, ['recover', 'refuse', 'block'])) {
            $post = Post::withTrashed()->where('post_id', $id)->first();
            if (!empty($post)) {
               if ($status == 'recover') { // 重新改为正常帖子
                   Post::withTrashed()->where('post_id', $id)->update(['post_audit'=>5, 'post_audited_at'=>$time]);
                  !empty($post['post_deleted_at']) && $post->restore();
                  $this->postRestore($post);
               }

               if (in_array($status, ['block', 'refuse'])) {
                   if (empty($post['post_deleted_at'])) {
                       $update = ['post_audit'=>-1, 'post_audited_at'=>$time, 'post_deleted_at'=>$time];
                       Post::where('post_id', $post['post_id'])->update($update);
                   }
                   if ($status == 'block') { // 封号
                       $this->destroy($post['id']);
                       if (!empty($userId)) {
                           // 执行封号操作
                           $this->block($userId);
                           // 删除用户三天内的帖子
                           // $start = date('Y-m-d H:i:s', time()-86400*3);
                           // $posts = Post::where('user_id', $userId)->whereBetween('post_created_at', [$start, $time])->get();
                           // $this->batchDestroy($posts);
                       }
                   }
                   if ($status == 'refuse') { // 拒绝
                       $this->destroy($post['id']);
                   }
               }
            }
            Report::where('reportable_id', $post['post_id'])->update(['status'=>1]);

        }

        return redirect(route('report::report.index'));

    }

    /**
     * @param $id
     * @return bool
     * 封号
     */
    public function block($id)
    {
        $client = new Client();
        $params = ['user_id'=>$id, 'time_stamp'=>time()];
        $params['signature'] = common_signature($params);
        try {
            $response = $client->request('POST', front_url('api/ry/set/block'), ['form_params' => $params]);
            $body     = $response->getBody();
            $result   = json_decode($body->getContents(),true);
            if($result['code']==200) {
                block_user($id);
            }
        } catch (GuzzleException $e) {
            return false;
        }
        return true;
    }

    /**
     * 被举报的用户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function user(Request $request)
    {
        $params = $this->baseReport($request, 'users');
        return view('backstage.report.user' , $params);
    }

    /**
     * 举报用户排行榜
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function reportUser(Request $request)
    {
        $params = $this->baseReport($request);
        return view('backstage.report.reportUser' , $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 被举报帖子
     */
    public function reportPost(Request $request)
    {
        $params = $this->baseReport($request, 'post');
        return view('backstage.report.reportPost' , $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 被举报帖子
     */
    public function reportPostFlow(Request $request)
    {
        $param = $request->all();
        $posts = Post::with('translations')
            ->join('reports', "posts.post_id", '=', 'reports.reportable_id')
            ->whereNull('posts.post_deleted_at')
            ->where(['reports.status'=>0, 'reports.reportable_type'=>'App\\Models\\Post'])
            ->paginate(10);
        $posts = collect($posts)->toArray();
        foreach ($posts['data'] as &$result) {
            $languages = array_filter(array_unique(['en', 'zh-CN', $result['post_content_default_locale'] ?? null]));
            $result['media'] = !empty($result['post_media']) ? json_decode($result['post_media'], true) : [];

            if (!empty($result['post_media'])) {
                $result['media'] = json_decode($result['post_media'], true);
                $type            = !empty($result['media']['image']) ? 'image' : 'video';
                $result['media'] = postMedia($type, $result['media'], 3);
            }

            $result['post_default_content'] = '';
            if (!empty($result['translations'])) {
                foreach ($result['translations'] as $translation) {
                    if ($result['post_content_default_locale'] == $translation['post_locale']) {
                        $result['post_default_content'] = $translation['post_content'];
                    }
                    if (in_array($translation['post_locale'], $languages)) {
                        if ($translation['post_locale']=='zh-CN') {
                            $result['trans']['cn'] = $translation['post_content'];
                        }
                        $result['trans'][$translation['post_locale']] = $translation['post_content'];
                    }
                }
            }
        }

        if (empty($param['page'])) {
            return view('backstage.content.audit.flow', ['result'=>$posts]);
        }
        return $posts;
    }

    public function history(Request $request)
    {
       $params = $this->baseReport($request);
       return view('backstage.report.history', $params);
    }

    /**
     * @param $request
     * @param string $functionType
     * @return mixed
     * 举报
     */
    private function baseReport($request, $functionType='')
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $params = $request->all();

        $params['appends']  = $params;
        $params['query']    = empty($uri['query']) ? "" : $uri['query'];

        $repId = $functionType ? 'reports.reportable_id' : 'reports.user_id';
        $type  = !empty($functionType) ? $functionType : $params['type'] ?? '';

        if (empty($params['details'])) {
            $table = $functionType=='post' ? 'posts' : 'users';
            $users = Report::select("$table.*", DB::raw('count(*) as num_all, f_reports.user_id as report_user_id'), 'reports.reportable_id', 'reports.created_at');
            if ($functionType=='post') {
                $users = $users->join('posts', "posts.post_id", '=', $repId);
                if(isset($params['is_delete']) && in_array($params['is_delete'], [0, 1])) {
                    if ($params['is_delete']==1) {
                        $users = $users->whereNotNull('posts.post_deleted_at');
                    } else {
                        $users = $users->whereNull('posts.post_deleted_at');
                    }
                }
            } else {
                $users = $users->join('users', "users.user_id", '=', $repId);
            }
            if (!empty($type)) {
                $reType = $type =='post' ? 'App\\Models\\Post' : 'App\\Models\\User';
                $users  = $users->where(['reports.reportable_type'=>$reType]);
            }
        } else {
            if (!empty($params['type']) && $params['type'] =='post') {
                $users = Report::select("reports.*", "posts.post_uuid")->join('posts', "posts.post_id", '=', 'reports.reportable_id');
            } else {
                $users = Report::select("reports.*");
            }
        }

        // 被举报用户/帖子
        if (isset($params['status'])) {
            $users = $users->where('reports.status', $params['status']);
        }
        if (!empty($params['dateTime'])) {
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $users   = $users->whereBetween('reports.created_at', [$start, $end]);
        }
        if (!empty($params['reportable_id'])) {
            $users   = $users->where('reports.reportable_id', $params['reportable_id']);
        }
        if (!empty($params['user_id'])) {
            $users   = $users->where('reports.user_id', $params['user_id']);
        }
        if (empty($params['details'])) {
            $users = $users->groupBy($repId);
        }
        $order = !empty($params['order']) ? 'num_all' : 'reports.id';
        $users = $users->orderByDesc($order);

        $users = $users->paginate(10);

        $block_users = block_user_list();
        $users->each(function ($item) use ($block_users){
            $item->is_block = intval(in_array($item->user_id , array_keys($block_users)));
            $item->user_format_created_at = Carbon::parse($item->user_created_at)->addHours(8)->toDateTimeString();
        });
        $params['users']=$users;

        return $params;

    }
}
