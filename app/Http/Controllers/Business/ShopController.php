<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Passport\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;

class ShopController extends Controller
{
    public function base(Request $request, $flag=false)
    {
        $params = $request->all();
        $userName = $params['user_name'] ?? '';
        $userPhone  = $params['user_phone'] ?? '';
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
            $shop = $shop->where('user_online', 0);
        }
        if (isset($params['user_delivery'])) {
            $shop = $shop->where('users.user_delivery', $params['user_delivery']);
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
        if (!empty($userName)) {
            $shop = $shop->where(function ($query) use ($userName){
                $query->where('users.user_name', 'like', "%{$userName}%")->orWhere('users.user_nick_name', 'like', "%{$userName}%");
            });
        }
        if (isset($params['user_verified'])) {
            $shop = $shop->where('users.user_verified', $params['user_verified']);
        }
        if (!empty($userPhone)) {
            $shop = $shop->where('users_phones.user_phone', $userPhone);
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

        $sort    = 'users.user_created_at';
        $shops   = $shop->where('users.user_shop', 1)->groupBy('users.user_id')->orderByDesc($sort)->paginate(10);
        $shopIds = $shops->pluck('user_id')->toArray();
        $points  = DB::connection('lovbee')->table('shop_evaluation_points')->whereIn('user_id', $shopIds)->get();
        foreach ($shops as $shop) {
            foreach ($points as $point) {
                if ($shop->user_id==$point->user_id) {
                    $sum = $point->point_1+$point->point_2+$point->point_3+$point->point_4+$point->point_5;
                    $shop->score   = $sum ? number_format((($point->point_1+$point->point_2*2+$point->point_3*3+$point->point_4*4+$point->point_5*5)/$sum), 2) : 0;
                    $shop->quality = $sum ? number_format(($point->quality/$sum), 2) : 0;
                    $shop->service = $sum ? number_format(($point->service/$sum), 2) : 0;
                }
            }
        }
        if ($flag) {
            $manager = DB::table('admins_shops')->whereIn('user_id', $shopIds)->get();
            foreach ($shops as $shop) {
                foreach ($manager as $item) {
                    if ($shop->user_id==$item->user_id) {
                        $shop->admin_username = $item->admin_username;
                        $shop->admin_id = $item->admin_id;
                    }
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
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $params = $this->base($request, 1);

        $admins = DB::table('admins')->select(DB::raw('admin_id id, admin_username title'))->get();
        $admins = collect($admins)->toArray();
        $admins = array_map(function ($arr) { return (array)$arr;}, $admins);
        $params['admins']  = $admins;
        return view('backstage.business.shop.index' , $params);
    }

    public function offline(Request $request)
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
        if(empty($phone))
        {
            abort(404);
        }
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
            $update = $this->url('api/backstage/shop', $info, 'PATCH', $header);
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

    public function review(Request $request)
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

        return view('backstage.business.shop.review', $params);
    }

    public function update(Request $request, $id)
    {
        $adminUser = auth()->user();
        $header    = ['HellooVersion' => '3.3.0', 'deviceId'=>$id];
        $fields  = array();
        $user_verified   = $request->input('user_verified');
        $user_delivery   = $request->input('user_delivery');
        $admin_id   = $request->input('admin_id');
        if($admin_id===null)
        {
            if($user_verified!==null)
            {
                $fields['user_verified'] = $user_verified=='yes'?1:0;
            }
            if($user_delivery!==null)
            {
                $fields['user_delivery'] = $user_delivery=='on'?1:0;
            }
            if(empty($fields))
            {
                abort(422 , 'Parameter cannot be empty!');
            }
            $connect = DB::connection('lovbee');
            try{
                $connect->beginTransaction();
                if(isset($fields['user_verified']))
                {
                    DB::table('business_audits')->insert([
                        'audit_id'  => $id,
                        'type'      => 'shop',
                        'date'      => date('Y-m-d'),
                        'status'    => $fields['user_verified']==1?'pass':'refuse',
                        'admin_id'  => $adminUser->admin_id,
                        'admin_username' => $adminUser->admin_username,
                        'created_at'=> date('Y-m-d H:i:s'),
                    ]);
                }
                $patch  = $this->url('api/backstage/shop/'.$id, $fields, 'PATCH', $header);
                if(!$patch)
                {
                    abort(500 , 'Front-end user information update error!');
                }
                $connect->commit();
            }catch (\Exception $e)
            {
                $connect->rollBack();
                Log::info('user_update_fail' , array(
                    'message'=>$e->getMessage(),
                    'data'=>$request->all(),
                ));
            }
        }else{
            $now = date('Y-m-d H:i:s');
            $adminUser = DB::table('admins_shops')->where('user_id' , $id)->first();
            $admin = DB::table('admins')->where('admin_id' , $admin_id)->first();
            if(empty($admin))
            {
                abort(404 , 'The administrator does not exist!');
            }
            if(empty($adminUser))
            {
                DB::table('admins_shops')->insert(array(
                    'admin_id'=>$admin_id,
                    'admin_username'=>$admin->admin_username,
                    'user_id'=>$admin_id,
                    'created_at'=>$now,
                    'updated_at'=>$now,
                ));
            }else{
                DB::table('admins_shops')->where('user_id' , $id)->update(array(
                    'admin_id'=>$admin_id,
                    'admin_username'=>$admin->admin_username,
                    'updated_at'=>$now,
                ));
            }
        }
        return response()->json(['result'=>'success']);
    }

    public function search(Request $request)
    {
        $params  = $request->all();
        $keyword = $request->input('keyword');
        $searches  = DB::connection('lovbee')->table('business_search_logs')->select(DB::raw('count(distinct user_id) userCount, count(content) contentCount, content'));
        if(isset($params['dateTime']))
        {
            $dateTime  = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$searches = $searches->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        if (!empty($keyword)) {
            $searches = $searches->where('content', 'like', "%{$keyword}%");
        }
        $params['searches'] = $searches->groupBy('content')->orderByDesc('contentCount')->paginate(10)->appends($params);
        $params['appends'] = $params;
        return view('backstage.business.shop.search' , $params);
    }

    public function searchShow(Request $request , $content)
    {
        $params  = $request->all();
        $searches  = DB::connection('lovbee')->table('business_search_logs')->where('content', $content)->orderByDesc('created_at');
        if(isset($params['dateTime']))
        {
            $dateTime  = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$searches = $searches->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        $searches  = $searches->paginate(10)->appends($params);
        $userIds = $searches->pluck('user_id')->unique()->toArray();
        $shopIds = $searches->pluck('owner')->unique()->toArray();
        $userIds = array_merge($userIds , $shopIds);
        $users   = User::whereIn('user_id', $userIds)->get();
        $searches->each(function($search) use ($users){
            $search->user = $users->where('user_id' , $search->user_id)->first();
            $search->shop = $users->where('user_id' , $search->owner)->first();
        });
        $params['appends'] = $params;
        $params['searches'] = $searches;
        return view('backstage.business.shop.search_show', $params);

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

    public function order(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('delivery_orders')->where('owner', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->paginate(10);

        $params['result'] = $result;
        return view('backstage.business.shop.order', $params);
    }

    public function owner(Request $request)
    {
        $userId  = $request->input('user_id');
        $adminId = $request->input('admin_id');
        $admin   = DB::table('admins')->where('admin_id', $adminId)->first();

        $info = DB::table('admins_shops')->where('user_id',$userId)->first();
        if (empty($info)) {
            DB::table('admins_shops')->insert([
                'user_id'=>$userId,
                'admin_id'=>$adminId,
                'admin_username'=> $admin->admin_username,
                'created_at'=>date('Y-m-d H:i:s'),
            ]);
        } else {
            DB::table('admins_shops')->where('user_id', $userId)->update([
                    'admin_id'=> $admin->admin_id,
                    'admin_username'=> $admin->admin_username,
            ]);
        }

        return response()->json(['result'=>'success']);
    }

    public function follow(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('users_follows')->where('user_id', $id);
        if (!empty($params['keyword'])) {
            $user    = User::where('user_name', 'like', "%{$params['keyword']}%")->orWhere('user_name', 'like', "%{$params['keyword']}%")->get();
            $userIds = $user->pluck('user_id')->toArray();
            $result  = $result->whereIn('followed_id', $userIds);
        }
        $result  = $this->dateTime($result, $params);
        $result  = $result->paginate(10);
        $userIds = $result->pluck('followed_id')->toArray();

        if (!empty($userIds)) {
            $users  = User::whereIn('user_id', $userIds)->get();
            foreach ($result as $item) {
                foreach ($users as $user) {
                    if ($item->followed_id == $user->user_id) {
                        $item->user_nick_name = $user->user_nick_name;
                        $item->user_name = $user->user_name;
                        $item->user_id = $user->user_id;
                        $item->user_avatar = $user->user_avatar;
                    }
                }
            }
        }

        $params['result'] = $result;
        return view('backstage.business.shop.follow', $params);
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
