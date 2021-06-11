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
    protected $status = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];

    public function index(Request $request)
    {
        $userId = $request->input('user_id' , '0');
        $user = auth()->user();
        if($user->admin_id==1)
        {
            $userIds = DB::table('admins_shops')->where('admin_id' , '!=' , 0)->get()->pluck('user_id')->toArray();
            $shops = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();
        }else{
            $userIds = DB::table('admins_shops')->where('admin_id' , $user->admin_id)->get()->pluck('user_id')->toArray();
            $shops = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();
        }
        $type = $request->input('type' , '0');
        $appends['type'] = $type;
        $appends['user_id'] = $userId;
        if($type=='0')
        {
            if($userId!=0)
            {
                $orders = DB::connection('lovbee')->table('delivery_orders')->where('owner' , $userId)->paginate(10)->appends($appends);
            }else{
                $orders = DB::connection('lovbee')->table('delivery_orders')->paginate(10)->appends($appends);
            }
        }else{
            if($userId!=0)
            {
                $orders = DB::connection('lovbee')->table('delivery_orders')->where('owner' , $userId)->where('status' , $type)->paginate(10)->appends($appends);
            }else{
                $orders = DB::connection('lovbee')->table('delivery_orders')->where('status' , $type)->paginate(10)->appends($appends);
            }
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
        return view('backstage.business.order.index' , compact('orders' , 'type' , 'shops' , 'userId'));
    }

    public function update(Request $request)
    {
        $params = $request->all();
        $status = $request->input('status' , null);
        $id     = $request->input('id' , '');
        $order  = DB::connection('lovbee')->table('delivery_orders')->where('order_id', $id)->first();
        $list   = range(1, 10);
        $update = [];

        if (in_array($status, $list)) { // 订单状态
            $time = intval((time()- strtotime($order->created_at))/60);
            $update = ['status'=>$status, 'order_time'=>$time, 'operator'=>auth()->user()->admin_id];
        }
        if (!empty($params['order_menu'])) { // 点菜单
            $update = ['menu'=>$params['order_menu']];
        }
        if (!empty($params['comment'])) { // 备注
            $update = ['comment'=>$params['comment']];
        }
        if (!empty($params['order_price'])) { // 订单价格
            $shopPrice = ($params['order_price'] - 30)*0.95;
            $update    = ['order_price'=>$params['order_price'], 'shop_price'=>$shopPrice];
        }
        Log::info('delivery_orders::update::', array_merge(['order_id'=>$id], $update));
        if(!empty($update)) {
            DB::transaction(function() use ($update, $id, $status, $list) {
                $update = array_merge($update, ['updated_at'=>date('Y-m-d H:i:s')]);
                DB::connection('lovbee')->table('delivery_orders')->where('order_id', $id)->update($update);

                // 插入Log 日志
                if (in_array($status, $list)) { // 订单状态
                    DB::table('delivery_orders_logs')->insert([
                        'order_id'  => $id,
                        'status'    => $status,
                        'admin_id'  => auth()->user()->admin_id,
                        'created_at'=> date('Y-m-d H:i:s')
                    ]);
                }
            });
        }
        return response()->json(['result'=>'success']);
    }
}
