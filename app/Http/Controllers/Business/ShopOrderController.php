<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Models\Passport\User;
use App\Models\Business\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class ShopOrderController extends Controller
{
    protected $status   = ['InProcess', 'Completed', 'Canceled'];

    protected $orderStatuses   = ['InProcess', 'Completed', 'Canceled'];

    protected $schedule = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];

    protected $schedules = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];

    protected $colorStyle = ['1'=>'white', '2'=>'yellow', '3'=>'orange', '4'=>'pink', '5'=>'green', '6'=>'blue', '7'=>'orange', '8'=>'gray', '9'=>'gray', '10'=>'gray'];

    protected $colorStyles = ['1'=>'white', '2'=>'yellow', '3'=>'orange', '4'=>'pink', '5'=>'green', '6'=>'blue', '7'=>'orange', '8'=>'gray', '9'=>'gray', '10'=>'gray'];

    public function index(Request $request)
    {
        $params = $data = $request->all();
        $user   = auth()->user();
        $schedule = intval($request->input('schedule' , 0));
        $dateTime = strval($request->input('dateTime' , ' - '));
        $data['schedule'] = $schedule;
        $shopId = intval($request->input('user_id' , 0));
        $adminsShops = DB::table('admins_shops');
        $user->admin_id!=1 && $adminsShops = $adminsShops->where('admin_id', $user->admin_id);
        $userIds= $adminsShops->get()->pluck('user_id')->unique()->toArray();
        $data['shops']  = User::whereIn('user_id', $userIds)->get();
        $schedules = $this->schedule;
        if (isset($params['status'])) {
            $data['status'] == 0 && $schedules = collect($schedules)->only('1','2','3','4');
            $data['status'] == 1 && $schedules = collect($schedules)->only('5');
            $data['status'] == 2 && $schedules = collect($schedules)->only('6' , '7', '8', '9', '10');
        }
        $data['schedules'] = $schedules;
        $orders = new Order();
        if (isset($params['status'])) {
            $status = intval($params['status']);
            $orders = $orders->where('status', $status);
        }
        if (!empty($schedule)) {
            $orders = $orders->where('schedule', $schedule);
        }
        $user->admin_id!=1 && $orders = $orders->where('operator', $user->admin_id);
        $shopId!=0  && $orders = $orders->where('shop_id', $shopId);
        $date_time = $this->parseTime($dateTime);
        if($date_time!==false)
        {
            $data['dateTime'] = $dateTime;
            $orders = $orders->whereBetween('created_at' , array($date_time['start'] , $date_time['end']));
        }
        $data['perPage'] = $perPage = 20;
        $orders   = $orders->orderByDesc('created_at')->paginate($perPage)->appends($params);
        $shopIds = $orders->pluck('shop_id')->unique()->toArray();
        $shops = User::whereIn('user_id' , $shopIds)->get();
        $time = Carbon::now()->subHour(8)->toDateTimeString();
        $orders->each(function($order) use ($shops , $time){
            $order->shop = $shops->where('user_id' , $order->shop_id)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->schedule==1 && $duration>300) || ($order->schedule==2 && $duration>600) || ($order->schedule==3 && $duration>780) || ($order->schedule==4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        $data['type' ] = $params['type'] ?? 0;
        $data['orders'] = $orders;
        $data['orderStatuses'] = $this->orderStatuses;
        $data['colorStyles']  = $this->colorStyles;
        $data['statusEncode'] = json_encode($this->schedule, true);
        $data['statusKv'] = array_map(function ($value, $key) {return ['title'=>trans('business.table.header.shop_order.'.$value), 'id'=>$key];}, $this->schedule, array_keys($this->schedule));
        return view('backstage.business.shop_order.index', $data);
    }

    public function update(Request $request , $id)
    {
        $params  = $request->all();
        $schedule= $request->input('schedule');
        $free_delivery = $request->input('free_delivery');
        $brokerage_percentage = $request->input('brokerage_percentage');
        $discount = $request->input('discount');
        $reduction = $request->input('reduction');
        $delivery_coast = $request->input('delivery_coast');
        $comment = $request->input('comment');
        $table   = 'orders';
        $order   = Order::where('order_id', $id)->firstOrFail();
        $time    = Carbon::now()->toDateTimeString();
        $schedules = array_keys($this->schedules);
        if (in_array($schedule, $schedules)) { // 订单状态
            $duration = intval((strtotime($time)- strtotime($order->created_at))/60);
            $schedule==5 && $orderState = 1;
            $schedule>=6  && $orderState = 2;
            $brokerage = $shopPrice = round($order->order_price*$order->brokerage_percentage/100 , 2);
            $data  = ['status'=>$orderState ?? 0, 'shop_price'=>$shopPrice, 'brokerage'=>$brokerage , 'schedule'=>$schedule, 'order_time'=>$duration, 'operator'=>auth()->user()->admin_id];
        }elseif ($free_delivery!==null)
        {
            if($free_delivery=='off')
            {
                $discountedPrice = $order->discounted_price+$order->delivery_coast;
                $data = array(
                    'free_delivery'=>0,
                    'discounted_price'=>$discountedPrice,
                    'profit'=>$discountedPrice-$order->brokerage,
                    'operator'=>auth()->user()->admin_id
                );
            }else{
                $discountedPrice = $order->discounted_price-$order->delivery_coast;
                $data = array(
                    'delivery_coast'=>0,
                    'free_delivery'=>1,
                    'discounted_price'=>$discountedPrice,
                    'profit'=>$discountedPrice-$order->brokerage,
                    'operator'=>auth()->user()->admin_id
                );
            }
        }elseif ($discount!==null)
        {
            $discount = round(floatval($discount) , 2);
            if($discount<0||$discount>100)
            {
                abort(422 , 'Wrong discount value!');
            }
            $discountedPrice = round($order->order_price*$discount/100+$order->delivery_coast , 2);
            $data = array(
                'discounted_price'=>$discountedPrice,
                'discount'=>$discount,
                'profit'=>$discountedPrice-$order->brokerage,
                'operator'=>auth()->user()->admin_id
            );
        }elseif ($reduction!==null)
        {
            $reduction = round(floatval($reduction) , 2);
            if($reduction<0)
            {
                abort(422 , 'Wrong amount of deduction!');
            }

            $discountedPrice = round($order->discounted_price+$order->reduction-$reduction , 2);
            $data = array(
                'discounted_price'=>$discountedPrice,
                'reduction'=>$reduction,
                'profit'=>$discountedPrice-$order->brokerage,
                'operator'=>auth()->user()->admin_id
            );
        }else if($delivery_coast!==null&&!$order->free_delivery)
        {
            $delivery_coast = round(floatval($delivery_coast) , 2);
            $discountedPrice = $order->discounted_price-$order->delivery_coast+$delivery_coast;
            $data = array(
                'delivery_coast'=>$delivery_coast,
                'discounted_price'=>$discountedPrice,
                'profit'=>$discountedPrice-$order->brokerage,
                'operator'=>auth()->user()->admin_id
            );
        }else if($brokerage_percentage!==null)
        {
            $brokerage_percentage = round(floatval($brokerage_percentage) , 2);
            $brokerage = round($order->order_price*$brokerage_percentage/100 , 2);
            $data = array(
                'brokerage_percentage'=>$brokerage_percentage,
                'brokerage'=>$brokerage,
                'profit'=>$order->discounted_price-$brokerage,
                'operator'=>auth()->user()->admin_id
            );
        }else if($comment!==null)
        {
            $data = array(
                'comment'=>strval($comment),
                'operator'=>auth()->user()->admin_id
            );
        }
        $order = $order->toArray();
        $order['id'] = Uuid::uuid1()->toString();
        $order['updated_at'] = $time;
        unset($order['format_price']);
        $order['detail']  = \json_encode($order['detail'],JSON_UNESCAPED_UNICODE);
        if(!empty($data)) {
            $data['updated_at'] = $time;
            try {
                DB::beginTransaction();
                DB::connection('lovbee')->table('orders_logs')->insert($order);
                DB::connection('lovbee')->table($table)->where('order_id', $id)->update($data);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error('order_update_fail', ['code'=>$exception->getCode(), 'message'=>$exception->getMessage(), 'params'=>$params]);
            }
        }
        return response()->json(['result'=>'success']);
    }

    public function show(Request $request, $orderId)
    {
        $params = $request->all();
        $order = Order::where('order_id', $orderId)->firstOrFail();
        $shop = User::where('user_id' , $order->shop_id)->first();
        $order->shop = $shop;
        $params['order'] = $order;
        $params['colorStyles'] = $this->colorStyles;
        $params['schedules'] = $this->schedules;
        return view('backstage.business.shop_order.show', $params);
    }

    public function browse(Request $request)
    {
        $roles    = DB::table('roles')->whereIn('name', ['administrator', 'calling center'])->get();
        $roleIds = $roles->pluck('id')->toArray();
        $hasRoles = DB::table('model_has_roles')->whereIn('role_id', $roleIds)->get();
        $adminIds = $hasRoles->pluck('model_id')->toArray();
        $admins  = DB::table('admins')->select('admin_id', 'admin_username', 'admin_realname')->where('admin_status', 1)->whereIn('admin_id', $adminIds)->get();
        $params = $data = $request->all();
        $schedule = intval($request->input('schedule' , 0));
        $data['schedule'] = $schedule;
        $adminId = intval($request->input('admin_id' , 0));
        $data['admin_id'] = $adminId;
        $adminsShops = DB::table('admins_shops');
        $adminId!=1&&$adminId!=0 && $adminsShops = $adminsShops->where('admin_id', $adminId);
        $userIds= $adminsShops->get()->pluck('user_id')->unique()->toArray();
        $data['shops']  = DB::connection('lovbee')->table('users')->whereIn('user_id', $userIds)->get();
        $data['schedules'] = $this->schedules;
        $orders = new Order();
        $adminId!=1&&$adminId!=0 && $orders = $orders->where('operator', $adminId);
        if($schedule==0)
        {
            $orders = $orders->whereIn('status', array(1,2));
        }else{
            $orders = $orders->where('schedule', $schedule);
        }
        $orders   = $orders->paginate(10)->appends($params);
        $shopIds = $orders->pluck('shop_id')->unique()->toArray();
        $shops = User::whereIn('user_id' , $shopIds)->get();
        $time = Carbon::now()->subHour(8)->toDateTimeString();
        $orders->each(function($order) use ($shops , $time){
            $order->shop = $shops->where('user_id' , $order->shop_id)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->schedule==1 && $duration>300) || ($order->schedule==2 && $duration>600) || ($order->schedule==3 && $duration>780) || ($order->schedule==4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        $data['admins' ] = $admins;
        $data['schedule' ] = $params['schedule'] ?? 0;
        $data['orders'] = $orders;
        $data['orderStatuses'] = $this->orderStatuses;
        $data['colorStyles']  = $this->colorStyles;
        $data['statusEncode'] = json_encode($this->schedule, true);
        $allMoney = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(discounted_price) discounted_price, sum(delivery_coast) delivery_coast, sum(brokerage) brokerage'))->where('status', 1)->first();
        $data['money'] = (array)$allMoney;
        return view('backstage.business.shop_order.browse', $data);
    }

}
