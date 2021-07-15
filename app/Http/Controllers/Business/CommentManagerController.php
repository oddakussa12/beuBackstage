<?php

namespace App\Http\Controllers\Business;

use App\Models\Business\Comment;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;

class CommentManagerController extends Controller
{
    /**
     * @return Factory|\Illuminate\Foundation\Application|View
     * 审核员管理
     * @throws Throwable
     */
    public function index()
    {
        $role    = DB::table('roles')->whereIn('name', ['administrator', 'Reviewer'])->get();
        $roleIds = $role->pluck('id')->toArray();
        $hasRole = DB::table('model_has_roles')->whereIn('role_id', $roleIds)->get();
        $userIds = $hasRole->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname', 'admin_status', 'admin_country')->whereIn('admin_id', $userIds)->paginate(10);
        $tTime   = [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')];
        $mTime   = [date('Y-m-01 00:00:00'), date('Y-m-d H:i:s')];
        $todayTotals = DB::table('business_audits')->where('type' , 'comment')->whereIn('admin_id' , $userIds)->whereBetween('created_at', $tTime)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        $monthTotals = DB::table('business_audits')->where('type' , 'comment')->whereIn('admin_id' , $userIds)->whereBetween('created_at', $mTime)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        $totals = DB::table('business_audits')->where('type' , 'comment')->whereIn('admin_id' , $userIds)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        $refuseTodayTotals = DB::table('business_audits')->where('type' , 'comment')->where('status', 'refuse')->whereIn('admin_id' , $userIds)->whereBetween('created_at', $tTime)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        $refuseMonthTotals = DB::table('business_audits')->where('type' , 'comment')->where('status', 'refuse')->whereIn('admin_id' , $userIds)->whereBetween('created_at', $mTime)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        $refuseTotals = DB::table('business_audits')->where('type' , 'comment')->where('status', 'refuse')->whereIn('admin_id' , $userIds)->select('admin_id', DB::raw('COUNT(*) AS total'))->groupBy('admin_id')->get();
        foreach ($admins as $admin) {
            $today = collect($todayTotals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $month = collect($monthTotals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $total = collect($totals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $todayRefuseTotal = collect($refuseTodayTotals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $monthRefuseTotal = collect($refuseMonthTotals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $refuseTotal = collect($refuseTotals->where('admin_id' , $admin->admin_id)->first())->get('total' , 0);
            $admin->today       = $today; // 今日审核
            $admin->month       = $month; // 本月审核
            $admin->total       = $total; // 总审核

            $admin->pass        = $total - $refuseTotal;//总通过数
            $admin->passDay     = $today - $todayRefuseTotal; // 本月通过数
            $admin->passMonth   = $month - $monthRefuseTotal; // 本月通过数

            $admin->refuseDay = $todayRefuseTotal; // 今日不通过
            $admin->refuseMonth = $monthRefuseTotal; // 本月不通过
            $admin->refuse      = $refuseTotal; // 不通过总数
        }
        return view('backstage.business.comment_manager.index' , compact('admins'));
    }

    /**
     * @param Request $request
     * 审核明细
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        $params = $request->all();
        $audits = DB::table('business_audits')->where('admin_id', $id)->where('type', 'comment');
        if (!empty($params['status'])) {
            $audits = $audits->where('status', strval($params['status']));
        }
        if(isset($params['dateTime']))
        {
            $dateTime = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$audits = $audits->where('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        $audits = $audits->orderByDesc('created_at')->paginate(10)->appends($params);
        if ($audits->isNotEmpty()) {
            $ids    = $audits->pluck('audit_id')->unique()->toArray();
            $comments   = Comment::whereIn('comment_id', $ids)->get();
            $audits->each(function ($audit) use ($comments){
                $audit->comment = $comments->where('comment_id' , $audit->audit_id)->first();
            });
        }
        $params['audits'] = $audits;
        return view('backstage.business.comment_manager.show', $params);
    }

}
