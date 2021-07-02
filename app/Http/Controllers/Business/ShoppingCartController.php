<?php

namespace App\Http\Controllers\Business;

use App\Models\Business\Goods;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Models\Passport\User;
use App\Models\Business\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class ShoppingCartController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $shoppingCarts = DB::connection('lovbee')->table('shopping_carts');
        if (!empty($params['shop_name'])) {
            $shopName = strval($params['shop_name']);
            $users   = User::where('user_name', $shopName)->orWhere('user_nick_name', $shopName)->get();
            $userIds = $users->pluck('user_id')->toArray();
            $shoppingCarts  = $shoppingCarts->whereIn('shop_id', $userIds);
        }
        if (!empty($params['user_name'])) {
            $userName = strval($params['user_name']);
            $users   = User::where('user_name', $userName)->orWhere('user_nick_name', $userName)->get();
            $userIds = $users->pluck('user_id')->toArray();
            $shoppingCarts  = $shoppingCarts->whereIn('user_id', $userIds);
        }
        if (!empty($params['goods_name'])) {
            $goodsName = strval($params['goods_name']);
            $goods   = DB::connection('lovbee')->table('goods')->where('name', $goodsName)->get();
            $goodsIds= $goods->pluck('id')->toArray();
            $shoppingCarts  = $shoppingCarts->whereIn('goods_id', $goodsIds);
        }
        if (!empty($params['dateTime'])) {
            $dateTime = $this->parseTime(strval($params['dateTime']) , 'subHours');
            if($dateTime!==false)
            {
                $shoppingCarts  = $shoppingCarts->whereBetween('created_at', [$dateTime['start'], $dateTime['end']]);
            }
        }
        if (!empty($params['sort'])) {
            $sort = strval($params['sort']);
            $shoppingCarts = $shoppingCarts->orderByDesc($sort);
        }

        $shoppingCarts  = $shoppingCarts->paginate(10)->appends($params);
        $shopIds = $shoppingCarts->pluck('shop_id')->toArray();
        $userIds = $shoppingCarts->pluck('user_id')->toArray();
        $goodsIds= $shoppingCarts->pluck('goods_id')->toArray();
        $ids     = array_diff(array_unique(array_merge($shopIds, $userIds)), ['', null]);

        $goods   = Goods::whereIn('id', $goodsIds)->get();
        $users   = User::whereIn('user_id', $ids)->select('user_id', 'user_name', 'user_nick_name', 'user_avatar')->get();
        $shoppingCarts->each(function($shoppingCart) use ($goods , $users){
            $shop = $users->where('user_id' , $shoppingCart->shop_id)->first();
            $user = $users->where('user_id' , $shoppingCart->user_id)->first();
            $g = $goods->where('id' , $shoppingCart->goods_id)->first();
            $shoppingCart->shop_name = $shop->user_name;
            $shoppingCart->shop_nick_name = $shop->user_nick_name;
            $shoppingCart->user_name = $user->user_name;
            $shoppingCart->user_nick_name = $user->user_nick_name;
            $shoppingCart->goods_name  = $g->name;
            $shoppingCart->goods_image = $g->image;
        });
        $params['shoppingCarts'] = $shoppingCarts;
        return view('backstage.business.shopping_cart.index', $params);
    }
}
