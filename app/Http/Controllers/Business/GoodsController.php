<?php

namespace App\Http\Controllers\Business;

use App\Models\Goods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $keyword= $params['keyword'] ?? '';
        $params = $request->all();
        $goods  = Goods::select(DB::raw('t_goods.*, t_shops.name shop_name, t_shops.nick_name shop_nick_name, t_goods_views.num view_num'))
            ->join('shops', 'goods.shop_id', '=', 'shops.id')
            ->leftjoin('goods_views', 'goods_views.goods_id', '=', 'goods.id');
        if (isset($params['recommend'])) {
            $goods = $goods->where('goods.recommend', $params['recommend']);
        }
        $goods = $this->dateTime($goods, $params, 'addHours', 'goods');

        if (!empty($keyword)) {
            $goods = $goods->where('goods.name', 'like', "%{$keyword}%");
        }
        if (!empty($params['shopName'])) {
            $shopName = $params['shopName'];
            $goods = $goods->where(function ($query) use ($shopName){
                $query->where('shops.name', 'like', "%{$shopName}%")->orWhere('nick_name', 'like', "%{$shopName}%");
            });
        }
        $sort  = !empty($params['sort']) ? $params['sort'] : 'created_at';
        $sort  = $sort == 'view_num' ? $sort : 'goods.'.$sort;
        $goods = $goods->groupBy('goods.id')->orderByDesc($sort)->paginate(10);
        foreach ($goods as $good) {
            $good->image = !empty($good->image) && !is_array($good->image) ? json_decode($good->image, true) : $good->image;
        }

        $params['query']   = $query;
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
        return [];
    }
}
