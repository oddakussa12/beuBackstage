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

    public function base($request)
    {
        $admin_id = $request->input('admin_id', '0');
        $userId   = $request->input('user_id', '0');
        $schedule = $request->input('type', '0');
        $status   = $request->input('status', '0');
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
                $orders = $orders->whereIn('owner', $shopIds);
            }
        } else {
            $userId!=0  && $orders = $orders->where('owner', $userId);
        }

        $orders   = $orders->paginate(10)->appends($params);
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
        $params['orders'] = $orders;
        $params['orderStatus'] = $this->status;
        $params['schedule']    = $schedule;
        $params['statusEncode'] = json_encode($this->schedule, true);
        return view('backstage.business.shopCartOrder.index', $params);
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

        $allMoney = DB::connection('lovbee')->table('orders')->select(DB::raw('sum(order_price) order_price, sum(shop_price) shop_price'))->where('status', 5)->first();
        $params['orders'] = $orders;
        $params['admins'] = $admins;
        $params['user_id']= $params['user_id'] ?? 0;
        $params['type']   = $params['type'] ?? 0;
        $params['status'] = $this->status;
        $params['money']  = (array)$allMoney;

        return view('backstage.business.order.manager', $params);

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
