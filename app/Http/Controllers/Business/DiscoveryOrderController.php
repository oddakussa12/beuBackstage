<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
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

    public function base($request)
    {
        $admin_id = $request->input('admin_id' , '0');
        $userId   = $request->input('user_id' , '0');
        $type     = $request->input('type' , '0');
        $state    = $request->input('status' , '0');
        $delivery = $request->input('user_delivery', '');
        $appends['type'] = $type;
        $appends['user_id'] = $userId;

        $orders = DB::connection('lovbee')->table('delivery_orders');
        if (!empty($state)) {
            $orders = $orders->where('status', '>=', $state);
        } else {
            $type!=0 && $orders = $orders->where('status', $type);
        }
        $admin_id!=0 && $orders = $orders->where('operator', $admin_id);
        if (!empty($delivery)) {
            if (empty($userId)) {
                $shops  = User::where(['user_delivery'=>1, 'user_shop'=>1])->get();
                $shopIds= $shops->pluck('user_id')->toArray();
                $orders = $orders->whereIn('owner', $shopIds);
            }
        } else {
            $userId!=0  && $orders = $orders->where('owner', $userId);
        }

        $orders = $orders->paginate(10)->appends($appends);

        $goodsIds = $orders->pluck('goods_id')->toArray();
        $goodsIds = array_unique(array_filter($goodsIds , function($goodsId){
            return !empty($goodsId);
        }));

        $goods    = empty($goodsIds) ? collect() : DB::connection('lovbee')->table('goods')->select('id', 'name')->whereIn('id' , $goodsIds)->get();
        $ownerIds = $orders->pluck('owner')->toArray();
        $userIds  = $orders->pluck('user_id')->toArray();
        $userIds  = array_unique(array_merge($userIds , $ownerIds));
        $users    = DB::connection('lovbee')->table('users')->select('user_id', 'user_nick_name', 'user_contact', 'user_address')->whereIn('user_id' , $userIds)->get();

        $orders->shops = $shops ?? [];
        $orders->each(function($order) use ($users , $goods){
            $order->shop = $users->where('user_id' , $order->owner)->first();
            $order->user  = $users->where('user_id' , $order->user_id)->first();
            $order->g     = $goods->where('id' , $order->goods_id)->first();
            $duration = time()-strtotime($order->created_at);
            if (($order->status==1 && $duration>300) || ($order->status==2 && $duration>600) || ($order->status==3 && $duration>780) || ($order->status==4 && $duration>3600)) {
                $order->color = 1;
            }
        });


        return $orders;
    }

    public function index(Request $request)
    {
        $userId = $request->input('user_id' , '0');
        $type   = $request->input('type' , '0');
        $orders = $this->base($request);
        $user   = auth()->user();

        $userIds= DB::table('admins_shops');
        $user->admin_id!=1 && $userIds = $userIds->where('admin_id', $user->admin_id);
        $userIds= $userIds->get()->pluck('user_id')->toArray();

        $shops  = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();
        $status = $this->status;
        $statusEncode = json_encode($status, true);
        return view('backstage.business.order.index' , compact('orders' , 'type' , 'shops' , 'userId', 'status', 'statusEncode'));
    }

    public function update(Request $request)
    {
        $params = $request->all();
        $state  = $request->input('status' , null);
        $id     = $request->input('id' , '');
        $table  = !empty($params['version']) ? 'orders' : 'delivery_orders';
        $order  = DB::connection('lovbee')->table($table)->where('order_id', $id)->first();

        if (empty($order)) {
            abort('The order information is wrong, please refresh the page and try again');
        }
        $shopId = !empty($params['version']) ? $order->shop_id : $order->owner;

        $list   = range(1, 10);
        $update = [];

        if (in_array($state, $list)) { // 订单状态
            $time    = intval((time()- strtotime($order->created_at))/60);
            $state==5 && $orderState = 1;
            $state>6  && $orderState = 2;
            $update  = ['status'=>$orderState ?? 0, 'schedule'=>$state, 'order_time'=>$time, 'operator'=>auth()->user()->admin_id];

            $deposit = DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->first();
            if (!empty($deposit) && $order->deposit=='0.00' && $state==5) {
                $update['deposit'] = $deposit->balance - $order->shop_price;
            }
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
            try {
                DB::beginTransaction();
                DB::connection('lovbee')->table($table)->where('order_id', $order->order_id)->update(array_merge($update, ['updated_at'=>date('Y-m-d H:i:s')]));

                if (!empty($update['deposit'])) {
                    DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->update(['balance'=>$update['deposit']]);
                }
                if (in_array($state, $list)) { // 插入Log 日志 订单状态
                    DB::table('delivery_orders_logs')->insert([
                       'order_id'  => $order->order_id,
                       'status'    => $state,
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

    public function manager(Request $request)
    {
        $request->offsetSet('status', 5);
        $orders = $this->base($request);
        $params = $request->all();
        $role    = DB::table('roles')->whereIn('name', ['administrator', 'calling center'])->get();
        $roleIds = $role->pluck('id')->toArray();
        $hasRole = DB::table('model_has_roles')->whereIn('role_id', $roleIds)->get();
        $userIds = $hasRole->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname')->where('admin_status', 1)->whereIn('admin_id', $userIds)->get();

        foreach ($orders as $order) {
            foreach ($admins as $admin) {
                if ($order->operator==$admin->admin_id) {
                    $order->admin_username=$admin->admin_username;
                }
            }
        }

        $allMoney = DB::connection('lovbee')->table('delivery_orders')->select(DB::raw('sum(order_price) order_price, sum(shop_price) shop_price'))->where('status', 5)->first();
//        $deliveryMoney = DB::connection('lovbee')->table('delivery_orders')->select(DB::raw('sum(order_price) order_price, sum(shop_price) shop_price'))->where('status', 5)->first();
//        $orderMoney    = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(shop_price) shop_price'))->where('status', 5)->first();
        $params['orders'] = $orders;
        $params['admins'] = $admins;
        $params['user_id']= $params['user_id'] ?? 0;
        $params['type']   = $params['type'] ?? 0;
        $params['status'] = $this->status;
        $params['money']  = (array)$allMoney;
        /*$params['money']  = [
            'order_price'=> $deliveryMoney->order_price + $orderMoney->order_price,
            'shop_price' => $deliveryMoney->shop_price + $orderMoney->shop_price,
        ];*/

        return view('backstage.business.order.manager', $params);

    }

    public function order(Request $request)
    {
        $request->offsetSet('status', 5);
        $params = $request->all();
        $orders = $this->base($request);

        $params['orders'] = $orders;
        $params['user_id']= $params['user_id'] ?? 0;
        $params['status'] = $this->status;

        return view('backstage.business.order.detail', $params);
    }

    /**
     * @param Request $request
     * 押金管理
     */
    public function deposits(Request $request)
    {
        $params = $request->all();
        // $money  = DB::connection('lovbee')->table('shops_deposits')->paginate(10);
        $shops  = User::select(DB::raw('t_shops_deposits.*'),'users.user_id', 'users.user_name', 'users.user_nick_name')->
                    where(['user_delivery'=>1, 'user_shop'=>1])->leftJoin('shops_deposits', 'shops_deposits.user_id', '=', 'users.user_id')->paginate(10);
         $params['shops'] = $shops;
        return view('backstage.business.order.deposits', $params);
    }

    public function depositsUpdate(Request $request)
    {
        $params = $request->all();
        if (empty($params['user_id'])) {
            Log::error(__FUNCTION__.' 押金修改:', $params);
            abort('404');
        }

        try {
            DB::beginTransaction();
            $time  = date('Y-m-d H:i:s');
            $base  = ['admin_id'=>auth()->user()->admin_id, 'admin_username'=>auth()->user()->admin_username];
            $money = DB::connection('lovbee')->table('shops_deposits')->where('user_id', $params['user_id'])->first();
            if (empty($money)) {
                $data = [
                    'user_id'   => $params['user_id'],
                    'money'     => $params['money'],
                    'balance'   => $params['money'],
                    'money_time'=> $params['money_time'],
                    'created_at'=> $time,
                ];
                $data['deposit_id'] = DB::connection('lovbee')->table('shops_deposits')->insertGetId($data);
            } else {
                $data['user_id']    = $params['user_id'];
                $data['updated_at'] = $time;
                $data['money']      = $money->money+$params['money'];
                $data['money_time'] = $params['money_time'];
                DB::connection('lovbee')->table('shops_deposits')->where('user_id', $params['user_id'])->update($data);
                $data['deposit_id'] = $money->id;
            }
            $log = DB::connection('lovbee')->table('shops_deposits_logs')->orderByDesc('id')->first();
            $data['created_at'] = $data['created_at'] ?? $time;
            !empty($params['money']) && $data['money'] = $params['money'];
            if (empty($log) || (!empty($log->money) && !empty($log->money_time))) {
                DB::connection('lovbee')->table('shops_deposits_logs')->insert(array_merge($data, $base));
            } else {
                DB::connection('lovbee')->table('shops_deposits_logs')->where('id', $log->id)->update(array_merge($data, $base));
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Transaction update:', ['code'=>$exception->getCode(), 'message'=>$exception->getMessage()]);
        }

        return response()->json(['result'=>'success']);
    }

    public function depositsMoney(Request $request, $id)
    {
        $params = $request->all();
        $logs = DB::connection('lovbee')->table('shops_deposits_logs')->where('user_id', $id)->orderByDesc('id')->paginate(10);

        $params['result'] = $logs;
        return view('backstage.business.order.depositsMoney', $params);
    }

}
