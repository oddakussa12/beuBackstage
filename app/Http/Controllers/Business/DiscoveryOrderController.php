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
        $userId = $request->input('user_id' , '0');
        $user = auth()->user();
        if ($user->admin_id==1) {
            $userIds = DB::table('admins_shops')->where('admin_id' , '!=' , 0)->get()->pluck('user_id')->toArray();
        } else {
            $userIds = DB::table('admins_shops')->where('admin_id' , $user->admin_id)->get()->pluck('user_id')->toArray();
        }
        $shops = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();

        $type = $request->input('type' , '0');
        $appends['type'] = $type;
        $appends['user_id'] = $userId;

        $orders = DB::connection('lovbee')->table('delivery_orders');
        if ($type!=0) {
            $orders = $orders->where('status', $type);
        }
        if ($userId!=0) {
            $orders = $orders->where('owner', $userId);
        }
        $orders = $orders->paginate(10)->appends($appends);

        $goodsIds = $orders->pluck('goods_id')->toArray();
        $goodsIds = array_unique(array_filter($goodsIds , function($goodsId){
            return !empty($goodsId);
        }));

        $goods    = empty($goodsIds) ? collect() : DB::connection('lovbee')->table('goods')->whereIn('id' , $goodsIds)->get();
        $ownerIds = $orders->pluck('owner')->toArray();
        $userIds  = $orders->pluck('user_id')->toArray();
        $userIds  = array_unique(array_merge($userIds , $ownerIds));
        $users    = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();

        $orders->each(function($order) use ($users , $goods){
            $order->owner = $users->where('user_id' , $order->owner)->first();
            $order->user  = $users->where('user_id' , $order->user_id)->first();
            $order->g     = $goods->where('id' , $order->goods_id)->first();
            $duration = time()-strtotime($order->created_at);
            if (($order->status==1 && $duration>300) || ($order->status==2 && $duration>600) || ($order->status==3 && $duration>780) || ($order->status==4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        /*dump(collect($orders)->toArray());
        exit;*/
        return view('backstage.business.order.index' , compact('orders' , 'type' , 'shops' , 'userId'));
    }

    public function update(Request $request)
    {
        $params = $request->all();
        $status = $request->input('status' , null);
        $id = $request->input('id' , '');
        if (in_array($status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])) { // 订单状态
            $update = ['status'=>$status];
        }
        if (!empty($params['order_menu'])) { // 点菜单
            $update = ['menu'=>$params['order_menu']];
        }
        if (!empty($params['comment'])) { // 备注
            $update = ['comment'=>$params['comment']];
        }
        if (!empty($params['order_price'])) { // 订单价格
            $shopPrice = ($params['order_price'] - 30)*0.95;
            $update = ['order_price'=>$params['order_price'], 'shop_price'=>$shopPrice];
        }

        if(!empty($update)) {
            $update = array_merge($update, ['updated_at'=>date('Y-m-d H:i:s')]);
            DB::connection('lovbee')->table('delivery_orders')->where('order_id', $id)->update($update);
        }
        return response()->json(['result'=>'success']);
    }
}
