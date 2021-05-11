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
        $params = $request->all();
        $now    = Carbon::now();
        $goods   = Goods::select(DB::raw('t_goods.*, t_shops.name shop_name, t_shops.nick_name shop_nick_name'))
            ->join('shops', 'goods.shop_id', '=', 'shops.id');
        $keyword= $params['keyword'] ?? '';
        if (isset($params['recommend'])) {
            $goods = $goods->where('goods.recommend', $params['recommend']);
        }

        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $goods = $goods->where('goods.created_at' , '>=' , $start)->where('goods.created_at' , '<=' , $end);
        }
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
        $goods = $goods->groupBy('goods.id')->orderByDesc("goods.{$sort}")->paginate(10);
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
