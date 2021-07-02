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
        $keyword= strval($request->input('keyword' , ''));
        $params = $request->all();
        $goods  = Goods::select(DB::raw('t_goods.*, t_users.user_name, t_users.user_nick_name, t_goods_views.num view_num'))
            ->join('users', 'goods.user_id', '=', 'users.user_id')
            ->leftjoin('goods_views', 'goods_views.goods_id', '=', 'goods.id');
        if (isset($params['recommendation'])&&in_array($params['recommendation'] , array(0 , 1 , '0' , '1'))) {
            $goods = $goods->where('goods.recommend', intval($params['recommendation']));
        }
        if(!empty($params['dateTime']))
        {
            $dateTime = strval($params['dateTime']);
            $dateTime = $this->parseTime($dateTime);
            $dateTime!==false&&$goods = $goods->whereBetween('goods.created_at', [$dateTime['start'], $dateTime['end']]);
        }

        if (!empty($params['goods_id'])) {
            $goods = $goods->where('goods.id', $params['goods_id']);
        }
        if (!empty($keyword)) {
            $goods = $goods->where('goods.name', 'like', "%{$keyword}%");
        }
        if (!empty($params['shop_name'])) {
            $shopName = strval($params['shop_name']);
            $goods = $goods->where(function ($query) use ($shopName){
                $query->where('users.user_name', 'like', "%{$shopName}%")->orWhere('user_nick_name', 'like', "%{$shopName}%");
            });
        }
        $sort  = $request->input('sort' , 'created_at');
        $sort  = $sort == 'view_num' ? $sort : 'goods.'.$sort;
        $goods = $goods->groupBy('goods.id')->orderByDesc($sort)->paginate(10);
        $goodsIds = $goods->pluck('id')->unique()->toArray();
        $categoryGoods = CategoryGoods::whereIn('goods_id' , $goodsIds)->get();
        $categoryIds = $categoryGoods->pluck('category_id')->unique()->toArray();
        $goodsCategories = GoodsCategory::whereIn('category_id' , $categoryIds)->get();
        $points   = DB::connection('lovbee')->table('goods_evaluation_points')->whereIn('goods_id', $goodsIds)->get();
        $goods->each(function ($g) use ($points,$goodsCategories){
            $point = $points->where('goods_id' , $g->id)->first();
            $category = $goodsCategories->where('goods_id' , $g->id)->first();
            $g->format_point = empty($point)?0:number_format((($point->point_1+$point->point_2*2+$point->point_3*3+$point->point_4*4+$point->point_5*5)/5), 2);
            $g->category = empty($category)?'':$category->name;
        });
        $params['appends'] = $params;
        $params['result']  = $goods;
        return view('backstage.business.goods.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $goods  = Goods::find($id);
        if (!empty($params['recommend'])) {
            $goods->recommend = $params['recommend'] == 'on';
            $goods->recommended_at = date('Y-m-d H:i:s');
            $goods->save();
        }
        return response()->json(['result' => 'success']);
    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $goods   = Goods::where('id', $id)->firstOrFail();
        $result = DB::connection('lovbee')->table('goods_views_logs')->where('goods_id', $id);
        if(!empty($params['dateTime']))
        {
            $dateTime = strval($params['dateTime']);
            $dateTime = $this->parseTime($dateTime);
            $dateTime!==false&&$result = $result->whereBetween('goods.created_at', [$dateTime['start'], $dateTime['end']]);
        }
        $result = $result->paginate(10);
        if ($result->isNotEmpty()) {
            $userIds = $result->pluck('user_id')->unique()->toArray();
            $shopIds = $result->pluck('owner')->unique()->toArray();
            $userIds = array_unique(array_merge($userIds, $shopIds));
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', $userIds)->get();
            $result->each(function($item) use ($users , $goods){
                $user = $users->where('user_id' , $item->user_id)->first();
                $shop = $users->where('user_id' , $item->owner)->first();
                $item->user_name = $user->user_name;
                $item->user_nick_name = $user->user_nick_name;
                $item->shop_name = $shop->user_name;
                $item->shop_nick_name = $shop->user_nick_name;
                $item->goods_name = $goods->name;
            });
        }
        $params['result'] = $result;
        return view('backstage.business.goods.view' , $params);
    }
}
