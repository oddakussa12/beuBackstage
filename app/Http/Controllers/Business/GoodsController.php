<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\Passport\User;
use App\Models\Business\Goods;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Business\CategoryGoods;
use App\Models\Business\GoodsCategory;

class GoodsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $goodsName = strval($request->input('goods_name' , ''));
        $appends['goods_name'] = $goodsName;
        $params = $appends = $request->all();
        $goods = new Goods();
        if (!empty($params['user_id'])) {
            $goods = $goods->where('user_id', intval($params['user_id']));
        }
        if (!empty($params['goods_id'])) {
            $goods = $goods->where('id', strval($params['goods_id']));
        }
        if (!empty($goodsName)) {
            $goods = $goods->where('name', 'like', "%{$goodsName}%");
        }
        if (isset($params['recommendation'])&&in_array($params['recommendation'] , array(0 , 1 , '0' , '1'))) {
            $goods = $goods->where('recommend', intval($params['recommendation']));
        }

        if(!empty($params['dateTime']))
        {
            $dateTime = strval($params['dateTime']);
            $dateTime = $this->parseTime($dateTime);
            $dateTime!==false&&$goods = $goods->whereBetween('created_at', [$dateTime['start'], $dateTime['end']]);
        }
        $sort  = $request->input('sort' , 'created_at');
        $sort  = $sort == 'created_at' ? $sort : 'like';
        $goods = $goods->orderByDesc($sort)->paginate(10)->appends($appends);
        $userIds = $goods->pluck('user_id')->unique()->toArray();
        $goodsIds = $goods->pluck('id')->unique()->toArray();
        $categoryGoods = CategoryGoods::whereIn('goods_id' , $goodsIds)->get();
        $categoryIds = $categoryGoods->pluck('category_id')->unique()->toArray();
        $goodsCategories = GoodsCategory::whereIn('category_id' , $categoryIds)->get();
        $views = DB::connection('lovbee')->table('goods_views')->whereIn('goods_id' , $goodsIds)->get();
        $shops = User::whereIn('user_id' , $userIds)->get();
        $goods->each(function($g) use ($shops , $views , $categoryGoods , $goodsCategories){
            $view = $views->where('goods_id' , $g->id);
            $g->shop = $shops->where('user_id' , $g->user_id)->first();
            $g->view = $view->first();
            $categoryId = $categoryGoods->where('goods_id' , $g->id)->first();
            $g->category = empty($categoryId)?'':$goodsCategories->where('category_id' , $categoryId->category_id)->first();
        });
        $params['appends'] = $appends;
        $params['goods']  = $goods;
        return view('backstage.business.goods.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $goods  = Goods::where('id' , $id)->firstOrFail();
        if (!empty($params['recommend'])) {
            $goods->recommend = $params['recommend'] == 'on';
            $goods->recommended_at = date('Y-m-d H:i:s');
        }
        if(isset($params['purchase_price']))
        {
            $purchase_price = round((float)$params['purchase_price'] , 2);
            $goods->purchase_price = $purchase_price;
        }
        if(isset($params['package_purchase_price']))
        {
            $package_purchase_price = round((float)$params['package_purchase_price'] , 2);
            $goods->package_purchase_price = $package_purchase_price;
        }
        if(isset($params['charge']))
        {
            $charge = (string)$params['charge'];
            $goods->charge = $charge;
        }
        $goods->save();
        return response()->json(['result' => 'success']);
    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $goods   = Goods::where('id', $id)->firstOrFail();
        $views = DB::connection('lovbee')->table('goods_views_logs')->where('goods_id', $id);
        if(!empty($params['dateTime']))
        {
            $dateTime = strval($params['dateTime']);
            $dateTime = $this->parseTime($dateTime);
            $dateTime!==false&&$views = $views->whereBetween('created_at', [$dateTime['start'], $dateTime['end']]);
        }
        $views = $views->paginate(10)->appends($params);
        if ($views->isNotEmpty()) {
            $userIds = $views->pluck('user_id')->unique()->toArray();
            $shopIds = $views->pluck('owner')->unique()->toArray();
            $userIds = array_unique(array_merge($userIds, $shopIds));
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', $userIds)->get();
            $views->each(function($item) use ($users , $goods){
                $user = $users->where('user_id' , $item->user_id)->first();
                $shop = $users->where('user_id' , $item->owner)->first();
                $item->user_name = empty($user->user_name)?'':$user->user_name;
                $item->user_nick_name = empty($user->user_nick_name)?'':$user->user_nick_name;
                $item->shop_name = empty($shop->user_name)?'':$shop->user_name;
                $item->shop_nick_name = empty($shop->user_nick_name)?'':$shop->user_nick_name;
                $item->goods_name = $goods->name;
            });
        }
        $params['views'] = $views;
        $params['appends'] = $params;
        return view('backstage.business.goods.view' , $params);
    }
}
