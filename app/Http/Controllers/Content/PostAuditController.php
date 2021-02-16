<?php

namespace App\Http\Controllers\Content;

use App\Models\Content\Audit;
use App\Models\Content\Post;
use App\Traits\PostTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Content\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function GuzzleHttp\Psr7\build_query;

class PostAuditController extends Controller
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
     * @param int $time
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $time = 1)
    {
        $param   = $request->all();
        $type    = $param['type'] ?? '';
        $type    = in_array($type, ['text', 'image', 'video', 'vote']) ? $type : '';

        $day = $time==1 ? 3 : 10;
        $utcTime   = Carbon::now('UTC');
        $adminId   = auth()->user()->admin_id;
        $today     = strtotime(date('Y-m-d 00:00:00'));
        $startTime = $time==1 ? date('Y-m-d H:i:s', strtotime($utcTime)-1800) : date('Y-m-d H:i:s', strtotime($utcTime)-86400*$day);
        $endTime   = date('Y-m-d H:i:s', strtotime($startTime)-86400*$day);
        $post      = Post::where(['post_audit'=>0, 'post_deleted_at'=>null]);
        if (!empty($param['id'])) {
            $post  = $post->where('post_id', $param['id']);
        } else {
            $post  = $post->whereBetween('post_created_at',[$endTime,$startTime]);
        }
        if ($type) {
            $post  = $post->where('post_type', $type);
        }
        if (!empty($param['skip']) && !empty($param['id'])) {
            $post  = $post->where('post_id', '<', $param['id']);
        }
        $count     = $post->count();

        $result    = $post->with('translations')->orderByDesc('post_created_at')->first();

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

        $totayCount= Audit::where('admin_id', $adminId)->where('created_at', '>=', $today)->count();
        $totalCount= Audit::where('admin_id', $adminId)->count();

        if (empty($result) && $time==1) {
            return $this->index($request, 3);
        }

        if (!empty($result)) {
            $result = collect($result)->toArray();

            $result['time']       = $time;
            $result['unaudited']  = $count;
            $result['todayCount'] = $totayCount ?? 0;
            $result['totalCount'] = $totalCount ?? 0;

            $languages = array_unique(['en', 'zh-CN', $result['post_content_default_locale'] ?? null]);
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
                        $result['trans'][$translation['post_locale']] = $translation['post_content'];
                    }
                }
            }
        } else {
            $result = [];
        }

        return view('backstage.content.audit.index', compact('result'));
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
        $uuid    = $param['uuid'] ?? '';
        $status  = $param['status'] ?? '';
        $time    = date('Y-m-d H:i:s');
        if (!empty($param['skip'])) {
            return redirect(route('content::audit.index').'?'.build_query($request->only('id','type','skip')));
        }
        if ($uuid && in_array($status, ['pass', 'hot', 'refuse', 'preheat'])) {
            $adminId = auth()->user()->admin_id;
            $post    = Post::where(['post_uuid'=>$uuid, 'post_audit'=>0])->first();
            if (!empty($post)) {
                $flag = true;
                if ($status=='refuse') {
                    $update = ['post_audit'=>-1, 'post_audited_at'=>$time, 'is_delete'=>2, 'post_deleted_at'=>date('Y-m-d H:i:s')];
                    try {
                       $flag = $this->destroy($post['post_id']);
                    } catch (GuzzleException $e) {
                        $flag = false;
                    }
                } elseif ($status=='hot') { // 热门
                    $update = ['post_audit'=>2, 'post_audited_at'=>$time];
                } else if($status == 'preheat') { // 预热
                    $update = ['post_audit'=>3, 'post_audited_at'=>$time];
                    try {
                        $flag = $this->setPreheat($post['post_id'] , 'on');
                    } catch (\Exception $exception) {
                        $flag = false;
                    }
                } else {
                    $update = ['post_audit'=>1, 'post_hotting'=>0, 'post_audited_at'=>$time];
                    try {
                       $flag = $this->update($request, $post['post_id']);
                    } catch (\Exception $exception) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    Post::withTrashed()->where('post_id', $post['post_id'])->update($update);
                    Audit::create([
                        'admin_id'       => $adminId,
                        'admin_user_name'=> auth()->user()->admin_username,
                        'user_id'        => $post['user_id'],
                        'source'         => '审核帖子',
                        'post_audit'     => $status,
                        'is_delete'      => $status=='refuse' ? 1 : 0,
                        'post_id'        => $post['post_id'],
                        'post_uuid'      => $uuid
                        ]
                    );
                }
            }
        }

        $type = !empty($param['type']) ? "?type=".$param['type'] : null;
        return redirect(route('content::audit.index').$type);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        $post = $this->post->find($id);
        $data = $request->all();

        if (!empty($data['post_hotting'])) {
           $result = $this->setNonFine($id , $data['post_hotting']==='on');
            if ($result) {
                $this->post->update($post , $data);
            }
            return $result;
        }
    }

    public function image($id)
    {
        $carousel_post_list = carousel_post_list($id);
        $supportLanguage = config('translatable.frontSupportedLocales');
        return view('backstage.content.post.image' , compact('id' , 'carousel_post_list' , 'supportLanguage'));
    }

}
