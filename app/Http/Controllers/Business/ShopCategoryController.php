<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ShopCategoryController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('promo_codes')->orderByDesc('created_at')->paginate(10);
        $params['result'] = $result;
        return view('backstage.business.promoCode.index', $params);

    }

    public function create()
    {
        return view('backstage.business.promoCode.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description'=> 'required|string|max:128',
            'promo_code' => 'required|string|max:16',
            'deadline'   => 'required|date',
            'reduction'  => 'filled|numeric',
            'percentage' => 'filled|numeric',
            'limit'      => 'filled|numeric',
        ]);
        $params = $request->all();
        unset($params['_token']);
        $params['id'] = Uuid::uuid1()->toString();
        $params['created_at'] = $params['updated_at'] = Carbon::now()->toDateTimeString();

        $info = DB::connection('lovbee')->table('promo_codes')->where('promo_code', $params['promo_code'])->first();
        if (!empty($info)) {
            abort(403, "Field PromoCode ({$params['promo_code']}) already exists");
        }
        try {
            DB::connection('lovbee')->table('promo_codes')->insert($params);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
        return response()->json(['result'=>'success']);
    }

    public function show($id)
    {
        $result = DB::connection('lovbee')->table('promo_codes')->where('id', $id)->first();
        return view('backstage.business.promoCode.edit', compact('result'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description'=> 'required|string|max:128',
            'promo_code' => 'required|string|max:16',
            'deadline'   => 'required|date',
            'reduction'  => 'filled|numeric',
            'percentage' => 'filled|numeric',
            'limit'      => 'filled|numeric',
        ]);
        $params = $request->all();

        $params['updated_at'] = Carbon::now()->toDateTimeString();
        unset($params['_token'], $params['id']);

        $info = DB::connection('lovbee')->table('promo_codes')->where('promo_code', $params['promo_code'])->where('id', '!=', $id)->first();
        if (!empty($info)) {
            abort(403, "Field PromoCode ({$params['promo_code']}) already exists");
        }

        try {
            DB::connection('lovbee')->table('promo_codes')->where('id', $id)->update($params);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
        return response()->json(['result'=>'success']);
    }

    public function destroy($id)
    {
        DB::connection('lovbee')->table('promo_codes')->where('id', $id)->delete();
        return response()->json(['result'=>'success']);

    }

}
