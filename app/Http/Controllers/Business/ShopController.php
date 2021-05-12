<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $shop = $shop->where('shops.created_at' , '>=' , $start)->where('shops.created_at' , '<=' , $end);
        }
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

}
