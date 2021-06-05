<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function base(Request $request)
    {
        $params = $request->all();
        $keyword= $params['keyword'] ?? '';
        $phone  = $params['phone'] ?? '';
        $country= config('country');
        $shop   = User::select(DB::raw('t_users.*,t_users_phones.*,t_users_countries.country, count(t_goods.id) num, t_shops_views.num view_num, t_recommendation_users.user_id recommend, t_recommendation_users.created_at recommended_at'))
            ->join('users_phones', 'users_phones.user_id', '=', 'users.user_id')
            ->leftjoin('users_countries', 'users_countries.user_id', '=', 'users.user_id')
            ->leftjoin('goods', 'goods.user_id', '=', 'users.user_id')
            ->leftjoin('shops_views', 'shops_views.owner', '=', 'users.user_id')
            ->leftjoin('recommendation_users', 'recommendation_users.user_id', '=', 'users.user_id');
        if (!empty($params['recommend'])) {
            $shop = $shop->whereNotNull('recommendation_users.user_id');
        }
        if (!empty($params['virtual'])) {
            $shop = $shop->where('user_online', $params['virtual']);
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
        if (!empty($params['country_code'])) {
            $country_code = $params['country_code'];
            if ($country_code !='other') {
                $shop = $shop->where('users_countries.country', strtolower($country_code));
            } else {
                $country_code = collect($country)->pluck('code')->toArray();
                $country_code = array_map('strtolower', $country_code);
                $shop = $shop->whereNotIn('users_countries.country', $country_code);
            }
        }

        $sort    = !empty($params['sort']) ? $params['sort'] : 'users.user_created_at';
        $shops   = $shop->where('users.user_shop', 1)->groupBy('users.user_id')->orderByDesc($sort)->paginate(10);
        $shopIds = $shops->pluck('user_id')->toArray();
        $points  = DB::connection('lovbee')->table('shop_evaluation_points')->whereIn('user_id', $shopIds)->get();

        foreach ($shops as $shop) {
            foreach ($points as $point) {
                if ($shop->user_id==$point->user_id) {
                    $sum = $point->point_1+$point->point_2+$point->point_3+$point->point_4+$point->point_5;
                    $shop->score   = $sum ? number_format((($point->point_1+$point->point_2*2+$point->point_3*3+$point->point_4*4+$point->point_5*5)/$sum), 2) : 0;
                    $shop->quality = $point->quality;
                    $shop->service = $point->service;
                }
            }
        }

        $params['appends'] = $params;
        $params['result']  = $shops;
        $params['countries']  = $country;

        return $params;

    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $params = $this->base($request);
        return view('backstage.business.shop.index' , $params);
    }

    public function virtual(Request $request)
    {
        $request->offsetSet('virtual', 1);
        $params = $this->base($request);
        return view('backstage.business.shop.virtual' , $params);
    }


    public function create()
    {
        return view('backstage.business.shop.create');
    }

    public function edit($id)
    {
        $shop    = User::find($id);
        $phone   = DB::connection('lovbee')->table('users_phones')->where('user_id', $id)->first();
        $country = DB::connection('lovbee')->table('users_countries')->where('user_id', $id)->first();
        return view('backstage.business.shop.edit', compact('shop', 'phone', 'country'));
    }

    public function show($id)
    {
        $user    = User::find($id);
        $phone   = DB::connection('lovbee')->table('users_phones')->where('user_id', $id)->first();
        $country = DB::connection('lovbee')->table('users_countries')->where('user_id', $id)->first();
        return view('backstage.business.shop.edit', compact('user', 'phone', 'country'));
    }

    public function store(Request $request)
    {
        $register = $request->only('user_phone_country', 'user_phone', 'user_name', 'user_nick_name', 'registration_type', 'password');
        $header   = ['HellooVersion' => '3.3.0', 'deviceId'=>$register['user_phone']];
        $result   = $this->url('/api/user/signUp', $register, 'POST', $header);
        $msg      = '';
        if ($result===true) {
            $user = DB::connection('lovbee')->table('users_phones')->where(['user_phone'=>$register['user_phone'], 'user_phone_country'=>trim($register['user_phone_country'], '+')])->first();
            $info = $request->all();
            $info['user_id'] = $user->user_id;
            $info['user_id'] = $user->user_id;
            $update = $this->url('/api/backstage/shop', $info, 'PATCH', $header);
            return [];
        } else{
            if ($result===false) {
                $msg = 'Server exception';
            }
            if (is_array($result) && !empty($result['message'])) {
                $msg = $result['message'];
            }
            $data = ['message'=>$msg];
        }
        return response()->json($data);
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
        $adminUser = auth()->user();
        $params = $request->all();
        if (!empty($params['recommend']) || isset($params['level']) || $params['audit']) {
            if (!empty($params['recommend'])) {
                $result = DB::connection('lovbee')->table('recommendation_users')->where('user_id', $id)->first();
                Log::info(__CLASS__.'::update:::recommend', ['user_id'=>$id, 'admin_username'=>$adminUser->admin_username, 'recommend'=>$params['recommend']]);
                if ($params['recommend']=='on') {
                    if (empty($result)) {
                        $insert = DB::connection('lovbee')->table('recommendation_users')->insert(['user_id'=>$id, 'created_at'=>date("Y-m-d H:i:s")]);
                        empty($insert) && abort('403', trans('common.ajax.result.prompt.fail'));
                    }
                } else {
                    DB::connection('lovbee')->table('recommendation_users')->where('user_id', $id)->delete();
                }
            }
            if (isset($params['level'])) {
                $result = User::where('user_id', $id)->update(['user_level'=>$params['level']=='on']);
                Log::info(__CLASS__.'::update:::level', ['user_id'=>$id, 'admin_username'=>$adminUser->admin_username, 'level'=>$params['level']]);
            }
            if (isset($params['audit'])) {
                $result = User::where('user_id', $id)->update(['user_verified'=>$params['audit']=='pass', 'user_verified_at'=>date('Y-m-d H:i:s')]);
                $data = [
                    'audit_id'  => $id,
                    'type'      => 'shop',
                    'date'      => date('Y-m-d'),
                    'status'    => $params['audit'],
                    'admin_id'  => $adminUser->admin_id,
                    'created_at'=> date('Y-m-d H:i:s'),
                    'admin_username' => $adminUser->admin_username,
                ];
                DB::table('business_audits')->insert($data);
            }
        } else {
            $user   = User::find($id);
            $info   = collect($params)->except('user_phone_country', 'user_phone')->toArray();
            $info   = array_diff($info, collect($user)->toArray());
            if (!empty($info)) {
                $result = User::where('user_id', $id)->update($info);
            }
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

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param false $json
     * @return bool|mixed|force(kmixed)
     */
    protected function url(string $url, array $data=[], string $method='POST', array $headers=[], bool $json=false)
    {
        try {
            $client = new Client();
            foreach ($data as &$datum) {
                $datum = is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : $datum;
            }
            $signature = common_signature($data);
            $data['signature'] = $signature;
            $data     = $json ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
            if(strtolower($method)=='get')
            {
                $response = $client->request($method, front_url($url), array('query'=>$data));
            }else{
                $response = $client->request($method, front_url($url), ['form_params'=>$data, 'headers'=>$headers]);
            }
            $code     = intval($response->getStatusCode());
            if ($code>=300) {
                Log::info('http_request_fail' , array('code'=>$code));
                return false;
            }
            $result = $response->getBody()->getContents();
            return json_decode($result, true);
        } catch (GuzzleException $e) {
            if (stripos($e->getMessage(), 'review')) {
                return true;
            } else {
                // dump('http_request_fail' , array('code'=>$e->getCode() , 'message'=>$e->getMessage()));
                Log::info('http_request_fail' , array('code'=>$e->getCode() , 'message'=>$e->getMessage()));
            }
            return ['code'=>$e->getCode(), 'message'=>$e->getMessage()];
        }
    }
}
