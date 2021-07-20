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
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $shops = DB::connection('lovbee')->table('users')->join('users_phones' , function ($join) use ($params){
            if(!empty($params['user_phone']))
            {
                $join->on('users_phones.user_id', '=', 'users.user_id')->where('users_phones.user_phone', '=', strval($params['user_phone']));
            }else{
                $join->on('users_phones.user_id', '=', 'users.user_id');
            }
        })->join('users_countries' , function ($join) use ($params){
            if(!empty($params['country_code']))
            {
                $join->on('users_countries.user_id', '=', 'users.user_id')->where('users_countries.country', '=', strtolower(strval($params['country_code'])));
            }else{
                $join->on('users_countries.user_id', '=', 'users.user_id');
            }
        })->leftJoin('shops_views' , function ($join) use ($params){
            $join->on('shops_views.owner', '=', 'users.user_id');
        })->leftJoin('shop_evaluation_points' , function($join){
            $join->on('shop_evaluation_points.user_id', '=', 'users.user_id');
        });
        if(isset($params['user_online'])&&$params['user_online']!==null)
        {
            $shops->where('users.user_online' , intval($params['user_online']));
        }
        if(isset($params['user_verified'])&&$params['user_verified']!==null)
        {
            $shops->where('users.user_verified' , intval($params['user_verified']));
        }
        if(isset($params['user_delivery'])&&$params['user_delivery']!==null)
        {
            $shops->where('users.user_delivery' , intval($params['user_delivery']));
        }
        if(isset($params['user_name'])&&$params['user_name']!==null)
        {
            $shops->where('users.user_name' , 'like' , "{$params['user_name']}%");
        }
        if(isset($params['dateTime']))
        {
            $dateTime = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$shops->whereBetween('users.user_created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        $shops = $shops->select([
            'users.user_id' , 'users.user_name' ,
            'users.user_about' , 'users.user_verified' ,
            'users.user_avatar' , 'users_phones.user_phone' ,
            'users.user_address' , 'users_phones.user_phone_country' ,
            'users.user_nick_name' , 'users_countries.country' , 'users.user_delivery' ,
            'users.user_level' , 'shops_views.num' , 'users.user_online',
            'shop_evaluation_points.point_1', 'users.user_created_at', 'users.user_verified_at',
            'shop_evaluation_points.point_2' , 'shop_evaluation_points.point_3',
            'shop_evaluation_points.point_4' , 'shop_evaluation_points.point_5',
            'shop_evaluation_points.quality' , 'shop_evaluation_points.service',
        ])->paginate(10)->appends($params);
        $admins = DB::table('admins')->select(DB::raw('admin_id id, admin_username title'))->get();
        $shopIds = $shops->pluck('user_id')->unique()->toArray();
        $managers = DB::table('admins_shops')->whereIn('user_id', $shopIds)->get();
        $shops->each(function($shop) use ($managers , $admins){
            $adminId = collect($managers->where('user_id' , $shop->user_id)->first())->get('admin_id' , 0);
            if(!empty($adminId))
            {
                $shop->admin = $admins->where('id' , $adminId)->first();
            }
            $sum = $shop->point_1+$shop->point_2+$shop->point_3+$shop->point_4+$shop->point_5;
            $shop->point = $sum ? number_format((($shop->point_1+$shop->point_2*2+$shop->point_3*3+$shop->point_4*4+$shop->point_5*5)/$sum), 2) : 0;
            $shop->format_quality = $sum ? number_format((($shop->quality)/$sum), 2) : 0;
            $shop->format_service = $sum ? number_format((($shop->service)/$sum), 2) : 0;
        });
        $admins = collect($admins)->toArray();
        $admins = array_map(function ($arr) { return (array)$arr;}, $admins);
        $params['admins']  = $admins;
        $params['shops']  = $shops;
        $params['countries']  = config('country');;
        return view('backstage.business.shop.index' , $params);
    }

    /**
     * @throws \Throwable
     */
    public function offline(Request $request)
    {
        $request->offsetSet('user_online', 0);
        return $this->index($request);
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
                $this->httpRequest('api/backstage/shop/'.$id, $fields, 'PATCH');
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
            $adminUser = DB::table('admins_shops')->where('admin_id' , $admin_id)->where('user_id' , $id)->first();
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
                    'user_id'=>$id,
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
        $views = DB::connection('lovbee')->table('shops_views_logs')->where('owner', $id);
        if(isset($params['dateTime']))
        {
            $dateTime = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$views->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        $views = $views->paginate(10)->appends($params);

        if ($views->isNotEmpty()) {
            $userIds = $views->pluck('user_id')->unique()->toArray();
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', $userIds)->get();
            $views->each(function($view) use ($users){
                $view->user = $users->where('user_id' , $view->user_id)->first();
            });
        }
        $params['views'] = $views;
        return view('backstage.business.shop.view' , $params);
    }

}
