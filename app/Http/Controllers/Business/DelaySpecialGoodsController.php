<?php


namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\Business\Goods;
use App\Http\Controllers\Controller;
use App\Models\Business\DelaySpecialGoods;
use App\Repositories\Contracts\UserRepository;
use App\Http\Requests\Business\DelaySpecialGoodsRequest;

class DelaySpecialGoodsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $delaySpecialGoods = DelaySpecialGoods::orderByDesc('created_at')->paginate($perPage);
        $goodsIds = $delaySpecialGoods->pluck('goods_id')->toArray();
        $shopIds = $delaySpecialGoods->pluck('shop_id')->unique()->toArray();
        $goods = Goods::whereIn('id' , $goodsIds)->get();
        $shops = app(UserRepository::class)->findByMany($shopIds);
        $delaySpecialGoods->each(function($specialG) use ($goods , $shops){
            $specialG->g = $goods->where('id' , $specialG->goods_id)->first();
            $specialG->shop = $shops->where('user_id' , $specialG->shop_id)->first();
        });
        return view('backstage.business.delay_special_goods.index' , compact('delaySpecialGoods'));
    }

    public function create()
    {
        $today = date("Y-m-d 23:59:59");
        $now = date("Y-m-d 00:00:00");
        $max = date("Y-m-d 23:59:59" , strtotime("+15 day"));
        $goodsId = request()->input('goods_id' , '');
        return view('backstage.business.delay_special_goods.create' , compact('today' , 'goodsId' , 'now' , 'max'));
    }

    public function edit($id)
    {
        $max = date("Y-m-d 23:59:59" , strtotime("+15 day"));
        $delaySpecialGoods = DelaySpecialGoods::where('id' , $id)->firstOrFail();
        $delaySpecialGoods->g = Goods::where('id' , $delaySpecialGoods->goods_id)->first();
        return view('backstage.business.delay_special_goods.edit' , compact('delaySpecialGoods' , 'max'));
    }

    public function store(DelaySpecialGoodsRequest $request)
    {
        $goodsId = strval($request->input('goods_id' , ''));
        $specialPrice = round(floatval($request->input('special_price' , 0)) , 2);
        $freeDelivery = intval($request->input('free_delivery' , 0));
        $packagingCost = round(floatval($request->input('packaging_cost' , 0)) , 2);
        $deadline = strval($request->input('deadline' , ''));
        $startTime = strval($request->input('start_time' , ''));
        if(date('Y-m-d H:i:s' , strtotime($deadline))!=$deadline)
        {
            abort(422 , 'deadline format error!');
        }
        if(date('Y-m-d H:i:s' , strtotime($startTime))!=$startTime)
        {
            abort(422 , 'start time format error!');
        }
        $goods = Goods::where('id' , $goodsId)->firstOrFail();
        $delaySpecialGoods = DelaySpecialGoods::where('goods_id' , $goodsId)->first();
        if(!empty($delaySpecialGoods))
        {
            abort(422 , 'Goods already exists!');
        }
        $data = array(
            'goods_id'=>$goods->id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'start_time'=>$startTime,
            'deadline'=>$deadline,
            'admin_id'=>auth()->user()->admin_id,
            'type'=>'store',
        );
        $this->httpRequest('api/backstage/delay_special_goods' , $data , 'patch');
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function update(DelaySpecialGoodsRequest $request , $id)
    {
        $specialPrice = round(floatval($request->input('special_price' , 0)) , 2);
        $freeDelivery = intval($request->input('free_delivery' , 0));
        $packagingCost = round(floatval($request->input('packaging_cost' , 0)) , 2);
        $deadline = strval($request->input('deadline' , ''));
        $startTime = strval($request->input('start_time' , ''));
        if(date('Y-m-d H:i:s' , strtotime($deadline))!=$deadline)
        {
            abort(422 , 'deadline format error!');
        }
        if(date('Y-m-d H:i:s' , strtotime($startTime))!=$startTime)
        {
            abort(422 , 'start time format error!');
        }
        DelaySpecialGoods::where('id' , $id)->firstOrFail();
        $data = array(
            'id'=>$id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'start_time'=>$startTime,
            'deadline'=>$deadline,
            'admin_id'=>auth()->user()->admin_id,
            'type'=>'update',
        );
        $this->httpRequest('api/backstage/delay_special_goods' , $data , 'patch');
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function destroy($id)
    {
        DelaySpecialGoods::where('id' , $id)->firstOrFail();
        $this->httpRequest('api/backstage/delay_special_goods' , array(
            'id'=>$id,
            'type'=>'destroy'
        ) , 'patch');
        return response()->json(array(
            'result'=>'success'
        ));
    }

}