<?php


namespace App\Http\Controllers\Business;

use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use App\Models\Business\Goods;
use Illuminate\Support\Facades\DB;
use App\Models\Business\SpecialGoods;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\SpecialGoodsRequest;

class SpecialGoodsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $specialGoods = SpecialGoods::orderByDesc('created_at')->paginate($perPage);
        $goodsIds = $specialGoods->pluck('goods_id')->toArray();
        $shopIds = $specialGoods->pluck('shop_id')->unique()->toArray();
        $goods = Goods::whereIn('id' , $goodsIds)->get();
        $shops = app(UserRepository::class)->findByMany($shopIds);
        $specialGoods->each(function($specialG) use ($goods , $shops){
            $specialG->g = $goods->where('id' , $specialG->goods_id)->first();
            $specialG->shop = $shops->where('user_id' , $specialG->shop_id)->first();
        });
        return view('backstage.business.special_goods.index' , compact('specialGoods'));
    }

    public function create()
    {
        $today = date("Y-m-d 23:59:59");
        $goodsId = request()->input('goods_id' , '');
        return view('backstage.business.special_goods.create' , compact('today' , 'goodsId'));
    }

    public function edit($id)
    {
        $specialGoods = SpecialGoods::where('id' , $id)->firstOrFail();
        $specialGoods->g = Goods::where('id' , $specialGoods->goods_id)->first();
        return view('backstage.business.special_goods.edit' , compact('specialGoods'));
    }

    public function store(SpecialGoodsRequest $request)
    {
        $goodsId = strval($request->input('goods_id' , ''));
        $specialPrice = round(floatval($request->input('special_price' , 0)) , 2);
        $freeDelivery = intval($request->input('free_delivery' , 0));
        $packagingCost = round(floatval($request->input('packaging_cost' , 0)) , 2);
        $deadline = strval($request->input('deadline' , ''));
        if(date('Y-m-d H:i:s' , strtotime($deadline))!=$deadline)
        {
            abort(422 , 'deadline format error!');
        }
        $goods = Goods::where('id' , $goodsId)->firstOrFail();
        $specialGoods = DB::connection('lovbee')->table('special_goods')->where('goods_id' , $goodsId)->first();
        if(!empty($specialGoods))
        {
            abort(422 , 'Goods already exists!');
        }
        $data = array(
            'goods_id'=>$goods->id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'admin_id'=>auth()->user()->admin_id,
            'type'=>'store',
        );
        $this->httpRequest('api/backstage/special_goods' , $data , 'patch');
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
        SpecialGoods::where('id' , $id)->firstOrFail();
        $data = array(
            'id'=>$id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'admin_id'=>auth()->user()->admin_id,
            'type'=>'update',
        );
        $this->httpRequest('api/backstage/special_goods' , $data , 'patch');
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function destroy($id)
    {
        SpecialGoods::where('id' , $id)->firstOrFail();
        $this->httpRequest('api/backstage/special_goods' , array(
            'id'=>$id,
            'admin_id'=>auth()->user()->admin_id,
            'type'=>'destroy'
        ) , 'patch');
        return response()->json(array(
            'result'=>'success'
        ));
    }
}