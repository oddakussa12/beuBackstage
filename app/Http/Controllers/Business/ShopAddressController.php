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

        $openTime = $request->input('open_time' , '');
        $closeTime = $request->input('close_time' , '');

        if(!empty($shopId)&&(!empty($openTime)||!empty($closeTime)))
        {
            $shop = DB::connection('lovbee')->table('users')->where('user_id' , $shopId)->first();
            $data = array(
                'user_updated_at'=>$now,
            );
            !empty($openTime)&&$data['open_time'] = $openTime;
            !empty($closeTime)&&$data['close_time'] = $closeTime;
            DB::connection('lovbee')->table('users')->where('user_id' , $shopId)->update($data);
        }


        return response()->json(array(
            'result'=>"success"
        ));
    }
}
