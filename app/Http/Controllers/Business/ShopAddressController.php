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
        if(!empty($shopId)&&(!empty($longitude)||!empty($latitude)))
        {
            $address = DB::connection('lovbee')->table('shops_addresses')->where('shop_id' , $shopId)->first();
            $data = array(
                'created_at'=>$now,
            );
            !empty($longitude)&&$data['longitude'] = $longitude;
            !empty($latitude)&&$data['latitude'] = $latitude;
            if(empty($address))
            {
                $data['shop_id'] = $shopId;
                DB::connection('lovbee')->table('shops_addresses')->insert($data);
            }else{
                DB::connection('lovbee')->table('shops_addresses')->where('shop_id' , $shopId)->update($data);
            }
        }
        return response()->json(array(
            'result'=>"success"
        ));
    }
}
