<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Concerns\BuildsQueries;


class DiscoveryOrderController extends Controller
{
    use BuildsQueries;

    public function index(Request $request)
    {
        $type = $request->input('type' , '0');
        $appends['type'] = $type;
        if($type=='0')
        {
            $orders = DB::connection('lovbee')->table('delivery_orders')->paginate(10)->appends($appends);
        }else{
            $orders = DB::connection('lovbee')->table('delivery_orders')->where('status' , $type)->paginate(10)->appends($appends);
        }
        $goodsIds = $orders->pluck('goods_id')->toArray();
        $goodsIds = array_unique(array_filter($goodsIds , function($goodsId){
            return !empty($goodsId);
        }));
        if(empty($goodsIds))
        {
            $goods = collect();
        }else{
            $goods = DB::connection('lovbee')->table('goods')->whereIn('id' , $goodsIds)->get();
        }
        $ownerIds = $orders->pluck('owner')->toArray();
        $userIds = $orders->pluck('user_id')->toArray();
        $userIds = array_unique(array_merge($userIds , $ownerIds));
        $users = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();
        $orders->each(function($order) use ($users , $goods){
            $order->owner = $users->where('user_id' , $order->owner)->first();
            $order->user = $users->where('user_id' , $order->user_id)->first();
            $order->g = $goods->where('id' , $order->goods_id)->first();
        });
        return view('backstage.business.order.index' , compact('orders' , 'type'));
    }

    public function update(Request $request)
    {
        $status = $request->input('status' , null);
        $id = $request->input('id' , '');
        if(in_array($status , array(1 , 2 , 3 , 4 , 5 ,6 ,7 , 8 , 9, 10)))
        {
            DB::connection('lovbee')->table('delivery_orders')->where('order_id' , $id)->update(
                array(
                    'status'=>$status,
                    'updated_at'=>date('Y-m-d H:i:s'),
                )
            );
        }
        return response()->json(array(
            'result'=>'success'
        ));
    }
}
