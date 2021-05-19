<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
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

        $sort  = !empty($params['sort']) ? $params['sort'] : 'users.user_created_at';
        $shops = $shop->where('users.user_shop', 1)->groupBy('users.user_id')->orderByDesc($sort)->paginate(10);

        $params['query']   = $query;
        $params['appends'] = $params;
        $params['result']  = $shops;

        /*dump(collect($shops)->toArray());
        exit;*/
        return view('backstage.business.shop.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $shop   = User::find($id);
        if (!empty($params['recommend'])) {
            $result = DB::connection('lovbee')->table('recommendation_users')->where('user_id', $id)->first();
            if ($params['recommend']=='on') {
                if (empty($result)) {
                    $insert = DB::connection('lovbee')->table('recommendation_users')->insert([
                        'user_id'=>$id, 'created_at'=>date("Y-m-d H:i:s")
                    ]);
                    empty($insert) && abort('403', '');
                }
            }
        }
        if (isset($params['level'])) {
            $shop->user_level = $params['level'] == 'on';
        }
        $shop->save();
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
        $result = DB::connection('lovbee')->table('shops_views_logs')->where('user_id', $id);
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
                    if ($item->shop_id==$user->user_id) {
                        $item->shop_name = $user->user_name;
                        $item->shop_nick_name = $user->user_nick_name;
                    }
                }

            }
        }

        $params['result'] = $result;
        return view('backstage.business.shop.view' , $params);
    }


}
