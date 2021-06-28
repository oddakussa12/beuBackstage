<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class OrderController extends Controller
{
    protected $status   = ['InProcess', 'Completed', 'Canceled'];
    protected $schedule = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];
    protected $colorStyle = ['1'=>'white', '2'=>'yellow', '3'=>'orange', '4'=>'pink', '5'=>'green', '6'=>'blue', '7'=>'orange', '8'=>'gray', '9'=>'gray', '10'=>'gray'];

    public function base($request)
    {
        $admin_id = $request->input('admin_id', '0');
        $userId   = $request->input('user_id', '0');
        $schedule = $request->input('type', '0');
        $status   = $request->input('status');
        $delivery = $request->input('user_delivery', '');
        $params   = $request->all();

        $orders = DB::connection('lovbee')->table('orders');
        if (isset($status)) {
            $orders = $orders->where('status', $status);
        }
        if (!empty($schedule)) {
            $orders = $orders->where('schedule', $schedule);
        }
        $admin_id!=0 && $orders = $orders->where('operator', $admin_id);
        if (!empty($delivery)) {
            if (empty($userId)) {
                $shops  = User::where(['user_delivery'=>1, 'user_shop'=>1])->get();
                $shopIds= $shops->pluck('user_id')->toArray();
                $orders = $orders->whereIn('shop_id', $shopIds);
            }
        } else {
            $userId!=0  && $orders = $orders->where('shop_id', $userId);
        }

        $orders   = $orders->paginate(10)->appends($params);
        $promoIds = array_diff($orders->pluck('promo_code')->unique()->toArray(), ['', null]);
        $userIds  = $orders->pluck('user_id')->toArray();
        $shopIds  = $orders->pluck('shop_id')->toArray();
        $userIds  = array_diff(array_unique(array_merge($userIds, $shopIds)), ['', null]);
        $users    = DB::connection('lovbee')->table('users')->select('user_id', 'user_nick_name', 'user_contact', 'user_address')->whereIn('user_id', $userIds)->get();
        $promoCode= DB::connection('lovbee')->table('promo_codes')->whereIn('id', $promoIds)->get();
        $orders->shops = $shops ?? [];
        $time = Carbon::now()->subHour(8)->toDateTimeString();

        $orders->each(function($order) use ($users, $promoCode, $time){
            $order->detail= !empty($order->detail) ? json_decode($order->detail, true) : [];
            $order->shop = $users->where('user_id', $order->shop_id)->first();
            $order->user = $users->where('user_id', $order->user_id)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->schedule==1 && $duration>300) || ($order->schedule==2 && $duration>600) || ($order->schedule==3 && $duration>780) || ($order->schedule==4 && $duration>3600)) {
                $order->color = 1;
            }
            foreach ($promoCode as $item) {
                if ($item->promo_code==$order->promo_code) {
                    $order->description = $item->description;
                    !empty($order->promo_code)    && $order->description    = $item->description;
                    empty($order->free_delivery)  && $order->free_delivery  = $item->free_delivery;
                    empty($order->reduction)      && $order->reduction      = $item->reduction;
                    empty($order->discount)       && $order->discount       = $item->discount;
                    empty($order->discount_type)  && $order->discount_type  = $item->discount_type;
                    empty($order->delivery_coast) && $order->delivery_coast = $item->delivery_coast;
                }
            }
            $order->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->addHours(3)->toDateTimeString();
            $order->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $order->updated_at)->addHours(3)->toDateTimeString();
        });

        return $orders;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $orders = $this->base($request);
        $user   = auth()->user();

        $userIds= DB::table('admins_shops');
        $user->admin_id!=1 && $userIds = $userIds->where('admin_id', $user->admin_id);
        $userIds= $userIds->get()->pluck('user_id')->toArray();

        $params['shops']  = DB::connection('lovbee')->table('users')->whereIn('user_id', $userIds)->get();

        $schedule = $this->schedule;
        if (isset($params['status'])) {
            $params['status'] == 0 && $schedule = collect($schedule)->only('1','2','3','4','6');
            $params['status'] == 1 && $schedule = collect($schedule)->only('5');
            $params['status'] == 2 && $schedule = collect($schedule)->only('7', '8', '9', '10');
        }
        $params['type' ] = $params['type'] ?? 0;
        $params['orders'] = $orders;
        $params['orderStatus'] = $this->status;
        $params['schedule']    = $schedule;
        $params['colorStyle']  = $this->colorStyle;
        $params['statusEncode'] = json_encode($this->schedule, true);
        $params['statusKv'] = array_map(function ($value, $key) {return ['title'=>$value, 'id'=>$key];}, $this->schedule, array_keys($this->schedule));

        return view('backstage.business.shopCartOrder.index', $params);
    }

    public function update(Request $request)
    {
        $params  = $request->all();
        $schedule= $request->input('status');
        $id      = $request->input('id' , '');
        $table   = 'orders';
        $order   = DB::connection('lovbee')->table($table)->where('order_id', $id)->first();
        $time    = Carbon::now()->subHour(8)->toDateTimeString();

        if (empty($order)) {
            abort('The order information is wrong, please refresh the page and try again');
        }
        $shopId = $order->shop_id;

        $list   = range(1, 10);
        $update = [];

        if (in_array($schedule, $list)) { // 订单状态
            $duration = intval((strtotime($time)- strtotime($order->created_at))/60);
            $schedule==5 && $orderState = 1;
            $schedule>6  && $orderState = 2;
            $shopPrice = ($order->order_price - 30)*0.95;
            $update  = ['status'=>$orderState ?? 0, 'shop_price'=>$shopPrice, 'schedule'=>$schedule, 'order_time'=>$duration, 'operator'=>auth()->user()->admin_id];
        }

        if (!empty($params['order_price']) || $schedule==5) { // 订单价格
            $orderPrice     = $params['order_price'];
            $freeDelivery   = $params['free_delivery'];
            $discountType   = $params['discount_type']  ?? $order->discount_type;
            $discount       = $params['discount']       ?? $order->discount;
            $reduction      = $params['reduction']      ?? $order->reduction;
            $deliveryCoast  = $params['delivery_coast'] ?? $order->delivery_coast;
            $discountedPrice= $orderPrice;
            $discountType && $discountedPrice = $discountType=='discount' ? $orderPrice*$discount/100 : $orderPrice-$reduction;

            $discountedPrice = round($discountedPrice+$deliveryCoast , 2);

            $shopPrice = $orderPrice*0.95; // 给商家的钱
            $update    = [
                'shop_price'=>$shopPrice,
                'discount'  =>$discount,
                'reduction' =>$reduction,
                'order_price'=>$params['order_price'],
                'free_delivery'=>$freeDelivery,
                'discount_type'=>$discountType,
                'delivery_coast'=>$deliveryCoast,
                'discounted_price'=>$discountedPrice,
            ];

            $deposit = DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->first();
            if (!empty($deposit) && $schedule==5) {
                $update['deposit'] = $deposit->balance+$order->shop_price - $shopPrice;
            }
            $order->admin_id = auth()->user()->admin_id;
            $order->admin_username = auth()->user()->admin_username;
            $order->created_at = $time;
        }

        if (!empty($params['comment'])) { // 备注
            $update = ['comment'=>$params['comment']];
        }


        Log::info('Order::update::', array_merge(['order_id'=>$id], $update, ['params'=>$params]));
        if(!empty($update)) {
            try {
                DB::beginTransaction();
                DB::connection('lovbee')->table('orders_logs')->insert((array)$order);

                DB::connection('lovbee')->table($table)->where('order_id', $order->order_id)->update(array_merge($update, ['updated_at'=>$time]));

                if (!empty($update['deposit'])) {
                    DB::connection('lovbee')->table('shops_deposits')->where('user_id', $shopId)->update(['balance'=>$update['deposit']]);
                }
                if (in_array($schedule, $list)) { // 插入Log 日志 订单状态
                    DB::table('Order_logs')->insert([
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

    public function show(Request $request, $orderId)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('orders')->where('order_id', $orderId)->first();
        if (!empty($result)) {
            $result->detail= !empty($result->detail) ? json_decode($result->detail, true) : [];
        }

        if (!empty($params['type']) && $params['type']=='goods') {
            $params['result'] = $result->detail ?? [];
            return view('backstage.business.shopCartOrder.view', $params);
        } else {
            if(!empty($result->promo_code)) {
                $promoCode= DB::connection('lovbee')->table('promo_codes')->where('id', $result->promo_code)->first();
                $result->discount_type = $promoCode->discount_type;
                empty($result->free_delivery) && $result->free_delivery = $promoCode->free_delivery;
                empty($result->reduction) && $result->reduction = $promoCode->reduction;
                empty($result->discount)  && $result->discount  = $promoCode->discount;
                empty($result->delivery_coast)  && $result->delivery_coast  = $promoCode->discount;
            }
            $params['result'] = $result;
            return view('backstage.business.shopCartOrder.price', $params);
        }

    }

    public function manager(Request $request)
    {
        $request->offsetSet('status', 2);
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

        $allMoney = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(discounted_price) discounted_price, sum(delivery_coast) delivery_coast, sum(shop_price) shop_price'))->where('status', 5)->first();
        $params['orders'] = $orders;
        $params['admins'] = $admins;
        $params['user_id']= $params['user_id'] ?? 0;
        $params['type']   = $params['type'] ?? 0;
        $params['money']  = (array)$allMoney;
        $params['schedule'] = $this->schedule;

        return view('backstage.business.shopCartOrder.manager', $params);
    }

    /**
     * @param Request $request
     * 购物车管理
     */
    public function shopCart(Request $request)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('shopping_carts');
        if (!empty($params['shopName'])) {
            $users   = User::where('user_name', 'like', "%{$params['shopName']}%")->orWhere('user_nick_name', 'like', "%{$params['shopName']}%")->get();
            $userIds = $users->pluck('user_id')->toArray();
            $result  = $result->whereIn('shop_id', $userIds);
        }
        if (!empty($params['userName'])) {
            $users   = User::where('user_name', 'like', "%{$params['userName']}%")->orWhere('user_nick_name', 'like', "%{$params['userName']}%")->get();
            $userIds = $users->pluck('user_id')->toArray();
            $result  = $result->whereIn('user_id', $userIds);
        }
        if (!empty($params['goodsName'])) {
            $goods   = DB::connection('lovbee')->table('goods')->where('name', 'like', "%{$params['goodsName']}%")->get();
            $goodsIds= $goods->pluck('id')->toArray();
            $result  = $result->whereIn('goods_id', $goodsIds);
        }
        if (!empty($params['dateTime'])) {
            $timezone= 3;
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->addHours($timezone)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->addHours($timezone)->toDateTimeString();
            $createAt= !empty($tablePre) ? "$tablePre.created_at" : 'created_at';
            $result  = $result->whereBetween($createAt, [$start, $end]);
        }
        if (!empty($params['sort'])) {
            $result = $result->orderByDesc($params['sort']);
        }

        $result  = $result->paginate(10);
        $shopIds = $result->pluck('shop_id')->toArray();
        $userIds = $result->pluck('user_id')->toArray();
        $goodsIds= $result->pluck('goods_id')->toArray();
        $ids     = array_diff(array_unique(array_merge($shopIds, $userIds)), ['', null]);

        $goods   = DB::connection('lovbee')->table('goods')->whereIn('id', $goodsIds)->get();
        $users   = User::whereIn('user_id', $ids)->select('user_id', 'user_name', 'user_nick_name', 'user_avatar')->get();
        foreach ($result as $item) {
            foreach ($users as $user) {
                if ($item->shop_id==$user->user_id) {
                    $item->shop_name = $user->user_name;
                    $item->shop_nick_name = $user->user_nick_name;
                }
                if ($item->user_id==$user->user_id) {
                    $item->user_name = $user->user_name;
                    $item->user_nick_name = $user->user_nick_name;
                }
            }
            foreach ($goods as $good) {
                if ($item->goods_id==$good->id) {
                    $item->goods_name  = $good->name;
                    $item->goods_image = !empty($good->image) ? json_decode($good->image, true) : [];
                }
            }
        }

        $params['result'] = $result;
        return view('backstage.business.order.shopCart', $params);
    }
}
