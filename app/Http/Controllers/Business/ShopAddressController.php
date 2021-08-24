<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ShopAddressController extends Controller
{
    public function update(Request $request)
    {
        $shopId = $request->input('shop_id' , '');
        $longitude = $request->input('longitude' , '');
        $latitude = $request->input('latitude' , '');
        $now = date('Y-m-d H:i:s');
        if(!empty($shopId)&&!empty($longitude)&&!empty($latitude))
        {
            $address = DB::connection('lovbee')->table('shops_addresses')->where('shop_id' , $shopId)->first();
            if(empty($address))
            {
                DB::connection('lovbee')->table('shops_addresses')->insert(array(
                    'shop_id'=>$shopId,
                    'longitude'=>$longitude,
                    'latitude'=>$latitude,
                    'created_at'=>$now,
                ));
            }else{
                DB::connection('lovbee')->table('shops_addresses')->where('shop_id' , $shopId)->update(array(
                    'longitude'=>$longitude,
                    'latitude'=>$latitude,
                    'created_at'=>$now,
                ));
            }
        }
        return response()->json(array(
            'result'=>"success"
        ));
    }
}
