<?php


namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\Business\Goods;
use Illuminate\Support\Facades\DB;
use App\Models\Business\SpecialGoods;
use App\Http\Controllers\V1\BaseController;
use App\Http\Requests\Business\SpecialGoodsRequest;

class SpecialGoodsController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = 10;
        $specialGoods = SpecialGoods::orderByDesc('created_at')->paginate($perPage);
        $goodsIds = $specialGoods->pluck('goods_id')->toArray();
        $goods = Goods::where('id' , $goodsIds)->get();
        $specialGoods->each(function($specialG) use ($goods){
            $specialG->g = $goods->where('id' , $specialG->goods_id)->first();
        });
        return view('backstage.business.special_goods.index' , compact('specialGoods'));
    }

    public function create()
    {
        return view('backstage.business.special_goods.create');
    }

    public function edit($id)
    {
        $specialGoods = SpecialGoods::where('id' , $id)->firstOrFail();
        $specialGoods->g = Goods::where('id' , $specialGoods->goods_id)->fisrt();
        return view('backstage.business.special_goods.edit' , compact('specialGoods'));
    }

    public function store(SpecialGoodsRequest $request)
    {
        $goodsIds = strval($request->input('goods_id' , ''));
        $specialPrice = round(floatval($request->input('special_price' , 0)) , 2);
        $freeDelivery = intval($request->input('free_delivery' , 0));
        $packagingCost = round(floatval($request->input('packaging_cost' , 0)) , 2);
        $deadline = strval($request->input('deadline' , ''));
        if(date('Y-m-d H:i:s' , strtotime($deadline))!=$deadline)
        {
            abort(422 , 'deadline format error!');
        }
        $goods = Goods::where('id' , $goodsIds)->firstOrFail();
        $now = date('Y-m-d H:i:s');
        DB::connection('lovbee')->table('special_goods')->insert(array(
            'shop_id'=>$goods->user_id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'created_at'=>$now,
            'updated_at'=>$now
        ));
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function update(SpecialGoodsRequest $request , $id)
    {
        $specialPrice = round(floatval($request->input('special_price' , 0)) , 2);
        $freeDelivery = intval($request->input('free_delivery' , 0));
        $packagingCost = round(floatval($request->input('packaging_cost' , 0)) , 2);
        $deadline = strval($request->input('deadline' , ''));
        if(date('Y-m-d H:i:s' , strtotime($deadline))!=$deadline)
        {
            abort(422 , 'deadline format error!');
        }
        $now = date('Y-m-d H:i:s');
        DB::connection('lovbee')->table('special_goods')->where('id' , $id)->update(array(
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'updated_at'=>$now
        ));
        return response()->json(array(
            'result'=>'success'
        ));
    }
}