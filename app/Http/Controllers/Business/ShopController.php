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
        $now    = Carbon::now();
        $shop   = Shop::select(DB::raw('t_shops.*, count(t_goods.id) num, t_shops_views.num view_num'), 'users.user_name', 'users.user_nick_name')
            ->leftjoin('goods', 'goods.shop_id', '=', 'shops.id')
            ->leftjoin('shops_views', 'shops_views.shop_id', '=', 'shops.id')
            ->join('users', 'users.user_id', '=', 'shops.user_id');
        $keyword= $params['keyword'] ?? '';
        if (isset($params['recommend'])) {
            $shop = $shop->where('shops.recommend', $params['recommend']);
        }
        if (isset($params['level'])) {
            $shop = $shop->where('shops.level', $params['level']);
        }
        $shop  = $this->dateTime($shop, $params, 'shops');
        if (!empty($keyword)) {
            $shop = $shop->where(function ($query) use ($keyword){
                $query->where('shops.name', 'like', "%{$keyword}%")->orWhere('shops.nick_name', 'like', "%{$keyword}%");
            });
        }
        if (!empty($params['userName'])) {
            $userName = $params['userName'];
            $shop = $shop->where(function ($query) use ($userName){
                $query->where('users.user_name', 'like', "%{$userName}%")->orWhere('users.user_nick_name', 'like', "%{$userName}%");
            });
        }

        $sort  = !empty($params['sort']) ? $params['sort'] : 'shops.created_at';
        $shops = $shop->groupBy('shops.id')->orderByDesc($sort)->paginate(10);

        $params['query']   = $query;
        $params['appends'] = $params;
        $params['result']  = $shops;

        return view('backstage.business.shop.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $shop   = Shop::find($id);
        if (!empty($params['recommend'])) {
            $shop->recommend = $params['recommend'] == 'on';
            $shop->recommended_at = date('Y-m-d H:i:s');
        }
        if (isset($params['level'])) {
            $shop->level = $params['level'] == 'on';
        }
        $shop->save();
        return [];
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

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('shops_views_logs')->where('shop_id', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->paginate(10);

        if ($result->isNotEmpty()) {
            $userIds = $result->pluck('user_id')->unique()->toArray();
            $shopIds = $result->pluck('shop_id')->unique()->toArray();
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', $userIds)->get();
            $shops   = Shop::select('id', 'name', 'nick_name')->whereIn('id', $shopIds)->get();
            foreach ($result as $item) {
                foreach ($users as $user) {
                    if ($item->user_id==$user->user_id) {
                        $item->user_name = $user->user_name;
                        $item->user_nick_name = $user->user_nick_name;
                    }
                }
                foreach ($shops as $shop) {
                    if ($item->shop_id==$shop->id) {
                        $item->shop_name = $shop->name;
                        $item->shop_nick_name = $shop->nick_name;
                    }
                }
            }
        }

        $params['result'] = $result;
        return view('backstage.business.shop.view' , $params);
    }

    public function dateTime($result, $params, $tablePre='')
    {
        if (!empty($params['dateTime'])) {
            $now    = Carbon::now();
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $createAt= !empty($tablePre) ? "$tablePre.created_at" : 'created_at';
            $result  = $result->where($createAt, '>=', $start)->where($createAt, '<=', $end);
        }

        return $result;
    }
}
