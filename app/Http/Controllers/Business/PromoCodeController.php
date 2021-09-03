<?php

namespace App\Http\Controllers\Business;

use App\Models\Business\Goods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $promoCodes = DB::connection('lovbee')->table('promo_codes')->orderByDesc('created_at')->paginate(10);
        $codes = $promoCodes->pluck('code')->toArray();
        $goodIds = DB::connection('lovbee')->table('promo_goods')->whereIn('code' , $codes)->get();
        $promoCodes->each(function($promoCode) use ($goodIds){
            $goodId = $goodIds->where('code' , $promoCode->promo_code)->first();
            $promoCode->goods_id = empty($goodId)?'':$goodId->goods_id;
        });
        $params['promoCodes'] = $promoCodes;
        return view('backstage.business.promo_code.index', $params);

    }

    public function rank()
    {
        $ranks = DB::connection('lovbee')->select(DB::raw('SELECT promo_code,count(*) as num from t_orders where status=1 GROUP BY promo_code ORDER BY num desc;'));
        $ranks = collect($ranks)->reject(function($rank , $k){
            return empty($rank->promo_code);
        });
        return view('backstage.business.promo_code.rank' , compact('ranks'));
    }

    public function create()
    {
        return view('backstage.business.promo_code.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description'=> 'required|string|max:128',
            'promo_code' => 'required|alpha_num|max:16|regex:/^[a-zA-Z0-9]{4,16}$/',
            'deadline'   => 'required|date',
            'reduction'  => 'filled|numeric',
            'percentage' => 'filled|numeric|between:0,100',
            'limit'      => 'filled|numeric',
        ]);
        $params = $request->all();
        unset($params['_token'] , $params['goods_id']);
        $params['id'] = Uuid::uuid1()->toString();
        $params['created_at'] = $params['updated_at'] = Carbon::now()->toDateTimeString();
        $goodsId = (string)$request->input('goods_id' , '');
        if(!empty($goodsId))
        {
            Goods::where('id' , $goodsId)->firstOrFail();
            $promoGoods = DB::connection('lovbee')->table('promo_goods')->where('promo_code' , $params['promo_code'])->first();
            if(!empty($promoGoods))
            {
                abort(403, "Field PromoCode ({$params['promo_code']}) already exists!");

            }
        }
        $promoCode = DB::connection('lovbee')->table('promo_codes')->where('promo_code', $params['promo_code'])->first();
        if (!empty($promoCode)) {
            abort(403, "Field PromoCode ({$params['promo_code']}) already exists");
        }
        try {
            DB::connection('lovbee')->table('promo_codes')->insert($params);
            !empty($goodsId)&&DB::connection('lovbee')->table('promo_goods')->insert(array(
                'code'=>$params['promo_code'],
                'goods_id'=>$goodsId,
            ));
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
        return response()->json(['result'=>'success']);
    }

    public function show($id)
    {
        $promoCode = DB::connection('lovbee')->table('promo_codes')->where('id', $id)->first();
        $code = DB::connection('lovbee')->table('promo_goods')->where('code' , $promoCode->promo_code)->first();
        $promoCode->goods_id = empty($code)?'':$code->goods_id;
        return view('backstage.business.promo_code.edit', compact('promoCode'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description'=> 'required|string|max:128',
            'deadline'   => 'required|date',
            'reduction'  => 'filled|numeric',
            'percentage' => 'filled|numeric|between:0,100',
            'limit'      => 'filled|numeric',
        ]);
        $params = $request->all();
        $goodsId = (string)$request->input('goods_id' , '');
        $params['updated_at'] = Carbon::now()->toDateTimeString();
        unset($params['_token'], $params['id'] , $params['goods_id']);

        $info = DB::connection('lovbee')->table('promo_codes')->where('promo_code', $params['promo_code'])->where('id', '!=', $id)->first();
        if (!empty($info)) {
            abort(403, "Field PromoCode ({$params['promo_code']}) already exists");
        }
        if(!empty($goodsId))
        {
            Goods::where('id' , $goodsId)->firstOrFail();
            $promoGoods = DB::connection('lovbee')->table('promo_goods')->where('code' , $params['promo_code'])->first();
        }
        try {
            DB::connection('lovbee')->table('promo_codes')->where('id', $id)->update($params);
            if(!empty($goodsId))
            {
                if(empty($promoGoods))
                {
                    DB::connection('lovbee')->table('promo_goods')->insert(array(
                        'code'=>$params['promo_code'],
                        'goods_id'=>$goodsId,
                    ));
                }else{
                    DB::connection('lovbee')->table('promo_goods')->where('code' , $params['promo_code'])->update(
                        array(
                            'goods_id'=>$goodsId
                        )
                    );
                }

            }
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
        return response()->json(['result'=>'success']);
    }

    public function destroy($id)
    {
        $code = DB::connection('lovbee')->table('promo_codes')->where('id', $id)->first();
        DB::connection('lovbee')->table('promo_codes')->where('id', $id)->delete();
        !empty($code)&&DB::connection('lovbee')->table('promo_goods')->where('code', $code->promo_code)->delete();
        return response()->json(['result'=>'success']);

    }

}
