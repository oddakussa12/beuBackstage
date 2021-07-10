<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Passport\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Business\DiscoveryOrder;


class DiscoveryOrderController extends Controller
{
    protected $statuses = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];
    protected $colorStyles = ['1'=>'white', '2'=>'yellow', '3'=>'orange', '4'=>'pink', '5'=>'green', '6'=>'blue', '7'=>'orange', '8'=>'gray', '9'=>'gray', '10'=>'gray'];

    public function index(Request $request)
    {
        $params = $data = $request->all();
        $user   = auth()->user();
        $status = intval($request->input('status' , 0));
        $data['status'] = $status;
        $shopId = intval($request->input('user_id' , 0));
        $data['userId'] = $shopId;
        $adminsShops = DB::table('admins_shops');
        $user->admin_id!=1 && $adminsShops = $adminsShops->where('admin_id', $user->admin_id);
        $userIds= $adminsShops->get()->pluck('user_id')->unique()->toArray();
        $data['shops']  = User::whereIn('user_id', $userIds)->get();
        $statuses = $this->statuses;
        $orders = new DiscoveryOrder();
        if (isset($params['status'])) {
            $status = intval($params['status']);
            $orders = $orders->where('status', $status);
        }
        $user->admin_id!=1 && $orders = $orders->where('operator', $user->admin_id);
        $shopId!=0  && $orders = $orders->where('owner', $shopId);
        $orders   = $orders->paginate(10)->appends($params);
        $shopIds = $orders->pluck('owner')->unique()->toArray();
        $shops = User::whereIn('user_id' , $shopIds)->get();
        $time = Carbon::now()->subHour(8)->toDateTimeString();
        $orders->each(function($order) use ($shops , $time){
            $order->shop = $shops->where('user_id' , $order->owner)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->status==1 && $duration>300) || ($order->status==2 && $duration>600) || ($order->status==3 && $duration>780) || ($order->status==4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        $data['statuses'] = $statuses;
        $data['type' ] = $params['type'] ?? 0;
        $data['orders'] = $orders;
        $data['colorStyles']  = $this->colorStyles;
        $data['statusKv'] = array_map(function ($value, $key) {return ['title'=>trans('business.table.header.shop_order.'.$value), 'id'=>$key];}, $statuses, array_keys($statuses));
        return view('backstage.business.discovery_order.index', $data);
    }

    public function update(Request $request)
    {
        $params  = $request->all();
        $schedule= $request->input('status' , null);
        $id      = $request->input('id' , '');
        $table   = !empty($params['version']) ? 'orders' : 'delivery_orders';
        $order   = DB::connection('lovbee')->table($table)->where('order_id', $id)->first();
        $time    = Carbon::now()->subHour(8)->toDateTimeString();

        if (empty($order)) {
            abort('The order information is wrong, please refresh the page and try again');
        }
        $shopId = !empty($params['version']) ? $order->shop_id : $order->owner;

        $list   = range(1, 10);
        $update = [];

        if (in_array($schedule, $list)) { // 订单状态
            $duration = intval((strtotime($time)- strtotime($order->created_at))/60);
            $schedule==5 && $orderState = 1;
            $schedule>6  && $orderState = 2;
            if (!empty($params['version'])) {
                $shopPrice = ($order->order_price - 30)*0.95;
                $update  = ['status'=>$orderState ?? 0, 'shop_price'=>$shopPrice, 'schedule'=>$schedule, 'order_time'=>$duration, 'operator'=>auth()->user()->admin_id];
            } else {
                $update  = ['status'=>$schedule, 'order_time'=>$duration, 'operator'=>auth()->user()->admin_id];
            }

            $deposit = DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->first();
            if (!empty($deposit) && $order->deposit=='0.00' && $schedule==5) {
                $update['deposit'] = $deposit->balance - $order->shop_price;
            }
        }
        if (!empty($params['order_menu'])) { // 点菜单
            $update = ['menu'=>$params['order_menu']];
        }
        if (!empty($params['comment'])) { // 备注
            $update = ['comment'=>$params['comment']];
        }
        if (empty($params['version']) && !empty($params['order_price'])) { // 订单价格
            $shopPrice = ($params['order_price'] - 30)*0.95;
            $update    = ['order_price'=>$params['order_price'], 'shop_price'=>$shopPrice];
        }

        Log::info('delivery_orders::update::', array_merge(['order_id'=>$id], $update));
        if(!empty($update)) {
            try {
                DB::beginTransaction();
                DB::connection('lovbee')->table($table)->where('order_id', $order->order_id)->update(array_merge($update, ['updated_at'=>$time]));

                if (!empty($update['deposit'])) {
                    DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->update(['balance'=>$update['deposit']]);
                }
                if (in_array($schedule, $list)) { // 插入Log 日志 订单状态
                    DB::table('delivery_orders_logs')->insert([
                       'order_id'  => $order->order_id,
                       'status'    => $schedule,
                       'admin_id'  => auth()->user()->admin_id,
                       'created_at'=> date('Y-m-d H:i:s')
                    ]);
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error('Transaction update:', ['code'=>$exception->getCode(), 'message'=>$exception->getMessage(), 'params'=>$params]);
            }
        }
        return response()->json(['result'=>'success']);
    }

    public function browse(Request $request)
    {
        $roles    = DB::table('roles')->whereIn('name', ['administrator', 'calling center'])->get();
        $roleIds = $roles->pluck('id')->toArray();
        $hasRoles = DB::table('model_has_roles')->whereIn('role_id', $roleIds)->get();
        $adminIds = $hasRoles->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname')->where('admin_status', 1)->whereIn('admin_id', $adminIds)->get();
        $params = $data = $request->all();
        $status = intval($request->input('status' , 0));
        $data['status'] = $status;
        $adminId = intval($request->input('admin_id' , 0));
        $data['admin_id'] = $adminId;
        $adminsShops = DB::table('admins_shops');
        $adminId!=1&&$adminId!=0 && $adminsShops = $adminsShops->where('admin_id', $adminId);
        $userIds= $adminsShops->get()->pluck('user_id')->unique()->toArray();
        $data['shops']  = DB::connection('lovbee')->table('users')->whereIn('user_id', $userIds)->get();
        $statuses = $this->statuses;
        $orders = new DiscoveryOrder();
        $adminId!=1&&$adminId!=0 && $orders = $orders->where('operator', $adminId);
        if($status!=0)
        {
            $orders = $orders->where('status', $status);
        }
        $orders   = $orders->paginate(10)->appends($params);
        $shopIds = $orders->pluck('owner')->unique()->toArray();
        $shops = User::whereIn('user_id' , $shopIds)->get();
        $time = Carbon::now()->subHour(8)->toDateTimeString();
        $orders->each(function($order) use ($shops , $time){
            $order->shop = $shops->where('user_id' , $order->owner)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->status==1 && $duration>300) || ($order->status==2 && $duration>600) || ($order->status==3 && $duration>780) || ($order->status==4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        $data['admins' ] = $admins;
        $data['orders'] = $orders;
        $data['statuses'] = $statuses;
        $data['colorStyles']  = $this->colorStyles;
        $allMoney = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(discounted_price) discounted_price, sum(delivery_coast) delivery_coast, sum(brokerage) brokerage'))->where('status', 1)->first();
        $data['money'] = (array)$allMoney;
        return view('backstage.business.discovery_order.browse', $data);

    }

}
