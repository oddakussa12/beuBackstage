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

class PostAuditFlowController extends Controller
{
    use PostTrait;

    private $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    /**
     * @param Request $request
     * @return \any[]|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 瀑布流读取图片
     */
    public function index(Request $request)
    {
        $param     = $request->all();
        $param['type'] = $request->input('type', 'image');

        $utcTime   = Carbon::now('UTC');
        $startTime = date('Y-m-d H:i:s', strtotime($utcTime)-300);
        $endTime   = date('Y-m-d H:i:s', strtotime($startTime)-86400*3);

        $posts = Post::with('translations')->whereBetween('post_created_at',[$endTime, $startTime])->where(['post_audit'=>0]);
        if ($param['type']!='all') {
            $posts = $posts->where('post_type', $param['type']);
        }
        if (!empty($param['user_id'])) {
            $posts = $posts->where('user_id', $param['user_id']);
        }
        $posts = $posts->orderByDesc('post_created_at')->paginate(20);

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

        $posts['query'] = $param;
        $posts['from']  = 'post';

        if (empty($param['page'])) {
            return view('backstage.content.audit.flow', ['result'=>$posts]);
        }

        return $posts;
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
        $ids     = $param['id'] ?? [];
        $status  = $param['status'] ?? '';
        $time    = date('Y-m-d H:i:s');

        if (empty($ids) || !in_array($status, ['pass', 'hot', 'refuse', 'preheat'])) {
            return '参数异常';
        }
        $posts   = Post::whereIn('post_id', $ids)->where(['post_audit'=>0])->get();
        if ($posts->isEmpty()) {
            return '参数异常!';
        }

        $params = [
            'post_id'  => collect($posts)->pluck('post_id')->all(),
            'time'     => time(),
            'type'     => $status,
            'operator' => auth()->user()->admin_username,
        ];

        if ($status=='refuse') { // 不通过
            $params['sql'] = ['post_audit'=>-1, 'post_audited_at'=>$time, 'is_delete'=>2, 'post_deleted_at'=>$time];
        }
        if ($status=='hot') { // 热门
            $params['sql'] = ['post_audit'=>2, 'post_audited_at'=>$time];
        }
        if($status == 'preheat') { // 预热
            $params['post_preheat'] = 1;
            $params['sql'] = ['post_audit'=>3, 'post_audited_at'=>$time];
        }
        if ($status=='pass') {
            $params['flag']= 0;
            $params['sql'] = ['post_audit'=>1, 'post_audited_at'=>$time, 'post_hotting'=>0];
        }

        // 发送请求至前端
        $this->httpRequest('api/bk/batch/post', $params);

        //Post::withTrashed()->whereIn('post_id', $ids)->update(array_merge($update, ['post_audited_at'=>$time]));
        $this->auditLog($posts, $status);

        $type = !empty($param['type']) ? "?type=".$param['type'] : null;
        return redirect(route('content::audit.flow').$type);

    }

    protected function auditLog($posts, $status)
    {
        $adminId = auth()->user()->admin_id;
        foreach ($posts as $post) {
            $insert[] = [
                'admin_id'       => $adminId,
                'admin_user_name'=> auth()->user()->admin_username,
                'user_id'        => $post['user_id'],
                'source'         => '审核帖子',
                'post_audit'     => $status,
                'is_delete'      => $status=='refuse' ? 1 : 0,
                'post_id'        => $post['post_id'],
                'post_uuid'      => $post['post_uuid'],
                'created_at'     => time(),
                'updated_at'     => time(),
            ];
        }
        !empty($insert) && Audit::insert($insert);
    }

}
