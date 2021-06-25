<?php

namespace App\Http\Controllers\Business;

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
        $params = $request->all();
        $params[$params['discount_type']] = $params['value'];
        unset($params['_token'], $params['value']);
        $params['id'] = Uuid::uuid1()->toString();
        $params['created_at'] = $params['updated_at'] = Carbon::now()->toDateTimeString();
        DB::connection('lovbee')->table('promo_codes')->insert($params);
        return response()->json(['result'=>'success']);
    }

    public function show($id)
    {
        $result = DB::connection('lovbee')->table('promo_codes')->where('id', $id)->first();
        return view('backstage.business.promoCode.edit', compact('result'));
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $params['reduction']  = '';
        $params['percentage'] = '';
        $params[$params['discount_type']] = $params['value'];
        $params['updated_at'] = Carbon::now()->toDateTimeString();
        unset($params['_token'], $params['value'], $params['id']);

        DB::connection('lovbee')->table('promo_codes')->where('id', $id)->update($params);
        return response()->json(['result'=>'success']);
    }

    public function destroy($id)
    {
        DB::connection('lovbee')->table('promo_codes')->where('id', $id)->delete();
        return response()->json(['result'=>'success']);

    }

}
