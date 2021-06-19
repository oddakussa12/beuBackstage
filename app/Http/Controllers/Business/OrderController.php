<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class OrderController extends Controller
{
    protected $status = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];

    public function base($request)
    {
        $admin_id = $request->input('admin_id', '0');
        $userId   = $request->input('user_id', '0');
        $type     = $request->input('type', '0');
        $state    = $request->input('status', '0');
        $delivery = $request->input('user_delivery', '');
        $appends['type'] = $type;
        $appends['user_id'] = $userId;

        $orders = DB::connection('lovbee')->table('orders');
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

        $orders   = $orders->paginate(10)->appends($appends);
        $ownerIds = $orders->pluck('owner')->toArray();
        $userIds  = $orders->pluck('user_id')->toArray();
        $userIds  = array_unique(array_merge($userIds, $ownerIds));
        $users    = DB::connection('lovbee')->table('users')->select('user_id', 'user_nick_name', 'user_contact', 'user_address')->whereIn('user_id', $userIds)->get();

        $orders->shops = $shops ?? [];
        $orders->each(function($order) use ($users){
            $order->detail= !empty($order->detail) ? json_decode($order->detail, true) : [];
            $order->shop = $users->where('user_id', $order->shop_id)->first();
            $order->user = $users->where('user_id', $order->user_id)->first();
            $duration = time()-strtotime($order->created_at);
            if (($order->status==1 && $duration>300) || ($order->status==2 && $duration>600) || ($order->status==3 && $duration>780) || ($order->status==4 && $duration>3600)) {
                $order->color = 1;
            }
        });


        return $orders;
    }

    public function index(Request $request)
    {
        $userId = $request->input('user_id', '0');
        $type   = $request->input('type', '0');
        $orders = $this->base($request);
        $user   = auth()->user();

        $userIds= DB::table('admins_shops');
        $user->admin_id!=1 && $userIds = $userIds->where('admin_id', $user->admin_id);
        $userIds= $userIds->get()->pluck('user_id')->toArray();

        $shops  = DB::connection('lovbee')->table('users')->whereIn('user_id', $userIds)->get();
        $status = $this->status;
        $statusEncode = json_encode($status, true);
        return view('backstage.business.shopCartOrder.index', compact('orders', 'type', 'shops', 'userId', 'status', 'statusEncode'));
    }

    public function show($orderId)
    {
        $result = DB::connection('lovbee')->table('orders')->where('order_id', $orderId)->first();
        if (!empty($result)) {
            $result->detail= !empty($order->detail) ? json_decode($order->detail, true) : [];
        }

        return view('backstage.business.shopCartOrder.view', compact('result'));

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

        $allMoney = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(shop_price) shop_price'))->where('status', 5)->first();
        $params['orders'] = $orders;
        $params['admins'] = $admins;
        $params['user_id']= $params['user_id'] ?? 0;
        $params['type']   = $params['type'] ?? 0;
        $params['status'] = $this->status;
        $params['money']  = (array)$allMoney;

        return view('backstage.business.order.manager', $params);

    }
}
