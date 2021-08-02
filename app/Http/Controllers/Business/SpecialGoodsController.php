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
        $shops = app(UserRepository::class)->findByMany($shopIds)->get();
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
            abort(422 , 'Product already exists!');
        }
        $now = date('Y-m-d H:i:s');
        $id = DB::connection('lovbee')->table('special_goods')->insertGetId(array(
            'shop_id'=>$goods->user_id,
            'goods_id'=>$goods->id,
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'created_at'=>$now,
            'updated_at'=>$now
        ));
        $this->httpRequest('api/backstage/special_goods' , array(
            'id'=>$id
        ) , 'patch');
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
        $goods = SpecialGoods::where('id' , $id)->firstOrFail();
        DB::connection('lovbee')->table('special_goods')->where('id' , $id)->update(array(
            'special_price'=>$specialPrice,
            'free_delivery'=>$freeDelivery,
            'packaging_cost'=>$packagingCost,
            'deadline'=>$deadline,
            'updated_at'=>$now
        ));
        $this->httpRequest('api/backstage/special_goods' , array(
            'id'=>$id
        ) , 'patch');
        $data = $goods->toArray();
        $data['admin_id'] = auth()->user()->admin_id;
        $data['log_updated_at'] = date('Y-m-d H:i:s');
        DB::connection('lovbee')->table('special_goods_logs')->insert($data);
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function destroy($id)
    {
        $goods = SpecialGoods::where('id' , $id)->firstOrFail();
        $this->httpRequest('api/backstage/special_goods' , array(
            'id'=>$id,
            'type'=>'destroy'
        ) , 'patch');
        DB::connection('lovbee')->table('special_goods')->where('id' , $id)->delete();
        $data = $goods->toArray();
        $data['admin_id'] = auth()->user()->admin_id;
        $data['log_updated_at'] = date('Y-m-d H:i:s');
        DB::connection('lovbee')->table('special_goods_logs')->insert($data);
        return response()->json(array(
            'result'=>'success'
        ));
    }
}