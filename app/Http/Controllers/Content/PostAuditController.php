<?php

namespace App\Http\Controllers\Content;

use App\Models\Content\Audit;
use App\Models\Post;
use App\Traits\PostTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PostAuditController extends Controller
{
    use PostTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $adminId = auth()->user()->admin_id;
        $today   = date('Y-m-d 00:00:00');
        $claim   = DB::table('posts_claim')->where('admin_id', $adminId)->first();
        $cCount  = DB::table('posts_claim')->where('admin_id', $adminId)->count();
        $result  = Post::with('owner')->where(['audited'=>0, 'is_deleted'=>0, 'post_id'=>$claim->post_id])->first();

        $count     = Post::where(['audited'=>0, 'is_deleted'=>0])->count();
        $totayCount= Audit::where('admin_id', $adminId)->where('created_at', '>=', $today)->count();
        $totalCount= Audit::where('admin_id', $adminId)->count();

        if (!empty($result)) {
            $result = collect($result)->toArray();
            $result['todayCount'] = $totayCount ?? 0;
            $result['totalCount'] = $totalCount ?? 0;
        }
        $result['claim']     = $cCount;
        $result['unaudited'] = $count;

        return view('backstage.content.audit.index', compact('result'));
    }

    /**
     * 领取审核帖子
     */
    public function claim()
    {
        $adminId = auth()->user()->admin_id;
        $result  = DB::table('posts_claim')->where('admin_id', $adminId)->first();
        if (empty($result)) {
            $claim  = DB::table('posts_claim')->get();
            $ids    = $claim->pluck('post_id')->toArray();
            $posts  = Post::where(['audited'=>0, 'is_deleted'=>0])->whereNotIn('post_id', $ids)->orderByDesc('created_at')->limit(10)->get();
            $data   = [];
            foreach ($posts as $post) {
                $data[] = ['admin_id' => $adminId, 'post_id'  => $post->post_id, 'created_at'=>date('Y-m-d H:i:s')];
            }
            DB::table('posts_claim')->insert($data);
        }
        return redirect(route('content::audit.index'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $param   = $request->all();
        $post_id = $param['post_id'] ?? '';
        $status  = $param['status']  ?? '';
        $time    = date('Y-m-d H:i:s');
        $adminId = auth()->user()->admin_id;

        if ($post_id && in_array($status, ['pass', 'refuse'])) {
            $post = Post::where(['post_id'=>$post_id, 'audited'=>0])->first();
            if (!empty($post)) {
                if ($status=='refuse') {
                    $flag = $this->destroy(['post_id'=>$post['post_id'], 'operator'=>auth()->user()->admin_username]);
                } else {
                    $flag = Post::where('post_id', $post['post_id'])->update(['audited'=>1, 'audited_at'=>$time]);
                }
                if ($flag) {
                    DB::table('posts_claim')->where(['post_id'=>$post_id, 'admin_id'=>$adminId])->delete();
                    Audit::create([
                        'admin_id'       => auth()->user()->admin_id,
                        'admin_user_name'=> auth()->user()->admin_username,
                        'user_id'        => $post['user_id'],
                        'post_id'        => $post_id,
                        'source'         => '审核帖子',
                        'audited'        => $status,
                        ]
                    );
                }
            }
        }

        $type = !empty($param['type']) ? "?type=".$param['type'] : null;
        return redirect(route('content::audit.index').$type);
    }

    public function image($id)
    {
        $carousel_post_list = carousel_post_list($id);
        $supportLanguage = config('translatable.frontSupportedLocales');
        return view('backstage.content.post.image' , compact('id' , 'carousel_post_list' , 'supportLanguage'));
    }

}
