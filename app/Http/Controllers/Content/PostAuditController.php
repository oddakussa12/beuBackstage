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
        if ($claim) {
            $result  = Post::with('owner')->where(['audited'=>0, 'is_deleted'=>0, 'post_id'=>$claim->post_id])->first();
        }
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
                /*if ($status=='refuse') {
                    $flag = $this->destroy(['post_id'=>$post['post_id'], 'operator'=>auth()->user()->admin_username]);
                } else {
                    $flag = Post::where('post_id', $post['post_id'])->update(['audited'=>1, 'audited_at'=>$time]);
                }*/

                $audited = $status=='refuse' ? -1 : 1;
                $flag = Post::where('post_id', $post['post_id'])->update(['audited'=>$audited, 'audited_at'=>$time]);

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

    public function jianHuangShi()
    {
        $role    = DB::table('roles')->where('name', 'jianhuang')->first();
        $hasRole = DB::table('model_has_roles')->where('role_id', $role->id)->get();
        $userIds = $hasRole->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname', 'admin_status')->whereIn('admin_id', $userIds)->get();
        $claims  = DB::table('posts_claim')->select(DB::raw('count(1) num, admin_id'))->groupBy('admin_id')->get();

        foreach ($admins as $admin) {
            $audits = DB::table('audits')->where('admin_id', $admin->admin_id);
            $tTime  = [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')];
            $mTime  = [date('Y-m-01'), date('Y-m-d H:i:s')];
            $today  = $audits->whereBetween('created_at', $tTime)->count(); // 今日已审核个数
            $month  = $audits->whereBetween('created_at', $mTime)->count(); // 本月已审核个数
            $total  = $audits->count(); // 总审核数
            $refuse = $audits->where('audited', 'refuse')->count(); // 拒绝总数
            $refuseMonth = $audits->where('audited', 'refuse')->whereBetween('created_at', $mTime)->count(); // 本月拒绝数
            $lastTime    = $audits->orderByDesc('created_at')->first();
            foreach ($claims as $claim) {
                if ($claim->admin_id==$admin->admin_id) {
                    $admin->todayClaim = $today+$claim->num; // 今日领取数
                    $admin->today      = $today; // 今日审核数
                    $admin->monthClaim = $month+$claim->num; // 本月领取数
                    $admin->month      = $month; // 本月审核数
                    $admin->totalClaim = $total+$claim->num; // 总领取数
                    $admin->total      = $total; // 总审核数
                    $admin->refuse     = $refuse; // 不通过总数
                    $admin->refuseMonth= $refuseMonth; // 本月不通过
                    $admin->lastTime   = !empty($lastTime->created_at) ? $lastTime->created_at : ''; // 最后审核时间
                }
            }
        }

        return view('backstage.content.post.manager' , compact('admins'));

    }

}
