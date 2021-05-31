<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $keyword= $params['keyword'] ?? '';
        $phone  = $params['phone'] ?? '';
        $shop   = User::select(DB::raw('t_users.*,t_users_phones.*,t_users_countries.country, count(t_goods.id) num, t_shops_views.num view_num, t_recommendation_users.user_id recommend, t_recommendation_users.created_at recommended_at'))
            ->join('users_phones', 'users_phones.user_id', '=', 'users.user_id')
            ->join('users_countries', 'users_countries.user_id', '=', 'users.user_id')
            ->leftjoin('goods', 'goods.user_id', '=', 'users.user_id')
            ->leftjoin('shops_views', 'shops_views.owner', '=', 'users.user_id')
            ->leftjoin('recommendation_users', 'recommendation_users.user_id', '=', 'users.user_id');
        if (isset($params['recommend'])) {
            $shop = $shop->where('recommend', $params['recommend']);
        }
        if (isset($params['level'])) {
            $shop = $shop->where('users.user_level', $params['level']);
        }
        if (!empty($params['dateTime'])) {
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->addHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->addHours(8)->toDateTimeString();
            $shop  = $shop->whereBetween('users.user_created_at', [$start, $end]);
        }
        if (!empty($keyword)) {
            $shop = $shop->where(function ($query) use ($keyword){
                $query->where('users.user_name', 'like', "%{$keyword}%")->orWhere('users.user_nick_name', 'like', "%{$keyword}%");
            });
        }
        if (isset($params['state'])) {
            $shop = $shop->where('users.user_verified', $params['state']);
        }
        if (!empty($phone)) {
            $shop = $shop->where('users_phones.user_phone', $phone);
        }

        $sort    = !empty($params['sort']) ? $params['sort'] : 'users.user_created_at';
        $shops   = $shop->where('users.user_shop', 1)->groupBy('users.user_id')->orderByDesc($sort)->paginate(10);
        $shopIds = $shops->pluck('user_id')->toArray();
        $points  = DB::connection('lovbee')->table('shop_evaluation_points')->whereIn('user_id', $shopIds)->get();

        foreach ($shops as $shop) {
            foreach ($points as $point) {
                if ($shop->user_id==$point->user_id) {
                    $sum = $point->point_1+$point->point_2+$point->point_3+$point->point_4+$point->point_5;
                    $shop->score   = number_format((($point->point_1+$point->point_2*2+$point->point_3*3+$point->point_4*4+$point->point_5*5)/$sum), 2);
                    $shop->quality = $point->quality;
                    $shop->service = $point->service;
                }
            }
        }

        $params['appends'] = $params;
        $params['result']  = $shops;

        return view('backstage.business.shop.index' , $params);
    }

    public function audit(Request $request)
    {
        $params  = $request->all();
        $keyword = $params['keyword'] ?? '';
        $result  = new User();
        if (!empty($keyword)) {
            $user = User::select(DB::raw('t_users.*'), 'users_phones.user_phone_country', 'users_phones.user_phone')->where('user_shop', 1)
                ->join('users_phones', 'users_phones.user_id', '=', 'users.user_id');
                $user->where(function ($query) use ($keyword) {
                    $query->where('users.user_name', 'like', "%{$keyword}%")->orWhere('users.user_nick_name', 'like', "%{$keyword}%")->orWhere('users_phones.user_phone', $keyword);
                });
           $result = $user->first();
        }
        $params['result'] = $result;

        return view('backstage.business.shop.audit', $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        if (!empty($params['recommend'])) {
            $result = DB::connection('lovbee')->table('recommendation_users')->where('user_id', $id)->first();
            if ($params['recommend']=='on') {
                if (empty($result)) {
                    $insert = DB::connection('lovbee')->table('recommendation_users')->insert([
                        'user_id'=>$id, 'created_at'=>date("Y-m-d H:i:s")
                    ]);
                    empty($insert) && abort('403', trans('common.ajax.result.prompt.fail'));
                }
            }
        }
        if (isset($params['level'])) {
            $result = User::where('user_id', $id)->update(['user_level'=>$params['level']=='on']);
        }
        if (isset($params['audit'])) {
            $result = User::where('user_id', $id)->update(['user_verified'=>$params['audit']=='pass', 'user_verified_at'=>date('Y-m-d H:i:s')]);
            $data = [
                'audit_id'  => $id,
                'type'      => 'shop',
                'date'      => date('Y-m-d'),
                'status'    => $params['audit'],
                'admin_id'  => auth()->user()->admin_id,
                'created_at'=> date('Y-m-d H:i:s'),
                'admin_username' => auth()->user()->admin_username,
            ];
            DB::table('business_audits')->insert($data);
        }
        return response()->json([]);
    }

    public function search(Request $request)
    {
        $params  = $request->all();
        $keyword = $request->input('keyword');
        $result  = DB::connection('lovbee')->table('business_search_logs')->select(DB::raw('count(distinct user_id) userCount, count(content) contentCount, content, created_at'));
        $result  = $this->dateTime($result, $params);
        if (!empty($keyword)) {
            $result = $result->where('content', 'like', "%{$keyword}%");
        }

        $params['result'] = $result->GroupBy('content')->paginate(10);
        return view('backstage.business.shop.search' , $params);
    }

    public function searchDetail(Request $request)
    {
        $params  = $request->all();
        $keyword = $params['keyword'];
        $result  = DB::connection('lovbee')->table('business_search_logs')->where('content', $keyword)->orderByDesc('created_at');
        $result  = $this->dateTime($result, $params);
        $result  = $result->paginate(10);
        $userIds = $result->pluck('user_id')->unique()->toArray();
        $users   = DB::connection('lovbee')->table('users')->whereIn('user_id', $userIds)->get();
        foreach ($result as $item) {
            foreach ($users as $user) {
                if ($item->user_id==$user->user_id) {
                    $item->user_name = $user->user_name;
                    $item->user_nick_name = $user->user_nick_name;
                    $item->user_avatar = $user->user_avatar;
                }
            }
        }
        $params['result'] = $result;
        return view('backstage.business.shop.searchDetail', $params);

    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('shops_views_logs')->where('owner', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->paginate(10);

        if ($result->isNotEmpty()) {
            $userIds = $result->pluck('user_id')->unique()->toArray();
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', array_merge($userIds, [$id]))->get();

            foreach ($result as $item) {
                foreach ($users as $user) {
                    if ($item->user_id==$user->user_id) {
                        $item->user_name = $user->user_name;
                        $item->user_nick_name = $user->user_nick_name;
                    }
                    if ($item->owner==$user->user_id) {
                        $item->shop_name = $user->user_name;
                        $item->shop_nick_name = $user->user_nick_name;
                    }
                }

            }
        }

        $params['result'] = $result;
        return view('backstage.business.shop.view' , $params);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 审核员管理
     */
    public function manager()
    {
        $role    = DB::table('roles')->where('name', 'jianhuang')->first();
        $role    = DB::table('roles')->where('name', 'administrator')->first();
        $hasRole = DB::table('model_has_roles')->where('role_id', $role->id)->get();
        $userIds = $hasRole->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname', 'admin_status', 'admin_country')->whereIn('admin_id', $userIds)->paginate(10);
        $claims  = DB::table('comments_claim')->select(DB::raw('count(1) num, admin_id'))->groupBy('admin_id')->get();

        foreach ($admins as $admin) {
            $where  = ['admin_id'=>$admin->admin_id, 'type'=>'comment'];
            $tTime  = [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')];
            $mTime  = [date('Y-m-01 00:00:00'), date('Y-m-d H:i:s')];
            $today  = DB::table('business_audits')->where($where)->whereBetween('created_at', $tTime)->count(); // 今日已审核个数
            $month  = DB::table('business_audits')->where($where)->whereBetween('created_at', $mTime)->count(); // 本月已审核个数
            $total  = DB::table('business_audits')->where($where)->count(); // 总审核数
            $refuse = DB::table('business_audits')->where($where)->where('status', 'refuse')->count(); // 拒绝总数
            $recommend      = DB::table('business_audits')->where($where)->where('status', 'recommend')->count(); // 推荐总数
            $refuseMonth    = DB::table('business_audits')->where($where)->where('status', 'refuse')->whereBetween('created_at', $mTime)->count(); // 本月拒绝数
            $recommendMonth = DB::table('business_audits')->where($where)->where('status', 'recommend')->whereBetween('created_at', $mTime)->count(); // 本月推荐数
            $lastTime       = DB::table('business_audits')->where($where)->orderByDesc('created_at')->first();

            $admin->todayClaim = $today; // 今日领取数
            $admin->monthClaim = $month; // 本月领取数
            $admin->totalClaim = $total; // 总领取数
            $admin->today      = $today; // 今日审核数
            $admin->month      = $month; // 本月审核数
            $admin->total      = $total; // 总审核数
            $admin->pass       = $total - $refuse - $recommend; // 通过总数
            $admin->passMonth  = $month - $refuseMonth - $recommendMonth; // 本月通过数
            $admin->refuse     = $refuse; // 不通过总数
            $admin->refuseMonth= $refuseMonth; // 本月不通过
            $admin->recommend  = $recommend; // 总推荐数
            $admin->recommendMonth = $recommendMonth; // 本月推荐数
            $admin->lastTime   = !empty($lastTime->created_at) ? $lastTime->created_at : ''; // 最后审核时间
            foreach ($claims as $claim) {
                if ($claim->admin_id==$admin->admin_id) {
                    $admin->todayClaim = $today+$claim->num; // 今日领取数
                    $admin->monthClaim = $month+$claim->num; // 本月领取数
                    $admin->totalClaim = $total+$claim->num; // 总领取数
                }
            }
        }

        return view('backstage.business.shop.manager' , compact('admins'));
    }

    /**
     * @param Request $request
     * 审核明细
     */
    public function managerDetail(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::table('business_audits')->where('admin_id', $id)->where('type', 'comment');

        if (!empty($params['status'])) {
            $result = $result->where('status', $params['status']);
        }
        $result = $this->dateTime($result, $params);
        $result = $result->orderByDesc('created_at')->paginate(10);
        if ($result->isNotEmpty()) {
            $ids    = $result->pluck('audit_id')->toArray();
            $list   = DB::connection('lovbee')->table('comments')->whereIn('comment_id', $ids)->get();
            foreach ($result as $item) {
                foreach ($list as $li) {
                    $li->media = !empty($li->media) && !is_array($li->media) ? json_decode($li->media, true) : $li->media;
                    if ($item->audit_id==$li->comment_id) {
                        $item->comment = $li;
                    }
                }
            }
        }
        $params['result'] = $result;
        return view('backstage.business.shop.managerDetail', $params);

    }
}
