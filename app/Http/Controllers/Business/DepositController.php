<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Database\Concerns\BuildsQueries;


class DepositController extends Controller
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

    public function create(Request $request, $id)
    {
        return view('backstage.business.deposit.create', compact('id'));
    }

    /**
     * @param Request $request
     * 押金管理
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $shops  = User::select(DB::raw('t_shops_deposits.*'),'users.user_id', 'users.user_name', 'users.user_nick_name')->
        where(['user_delivery'=>1, 'user_shop'=>1])->leftJoin('shops_deposits', 'shops_deposits.user_id', '=', 'users.user_id')->paginate(10);
        $params['shops'] = $shops;
        return view('backstage.business.deposit.index', $params);
    }

    public function order(Request $request)
    {
        $request->offsetSet('status', 5);
        $params  = $request->all();
        $version = $request->input('version');
        $orders  = (!empty($version)) ? $this->newOrder($request) : $this->base($request);
        $params['orders']  = $orders;
        $params['user_id'] = $params['user_id'] ?? 0;
        $params['status']  = $this->status;

        return view('backstage.business.deposit.detail', $params);
    }

    public function newOrder(Request $request)
    {
        $userId = $request->input('user_id' , '0');
        $orders = DB::connection('lovbee')->table('orders')->where('shop_id', $userId)->where('status', 5)->orderByDesc('created_at')->paginate(10);
        $shop   = User::where('user_id', $userId)->first();
        foreach ($orders as $order) {
            $order->shop = $shop;
        }
        return $orders;
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $this->validate($request, [
            'user_id' => 'required|string',
            'money' => 'required|string',
            'currency' => 'required|string',
            'dateTime' => 'required|string',
        ]);
        try {
            DB::beginTransaction();
            $time  = date('Y-m-d H:i:s');
            $base  = ['admin_id'=>auth()->user()->admin_id, 'admin_username'=>auth()->user()->admin_username];
            $money = DB::connection('lovbee')->table('shops_deposits')->where('user_id', $params['user_id'])->first();
            $data  = [
                'user_id'   => $params['user_id'],
                'money_time'=> $params['dateTime'],
            ];
            if (empty($money)) {
                $data += [
                    'money'     => $params['money'],
                    'balance'   => $params['money'],
                    'created_at'=> $time,
                ];
                $data['deposit_id'] = DB::connection('lovbee')->table('shops_deposits')->insertGetId($data);
            } else {
                $data['money']      = $money->money+$params['money'];
                $data['balance']    = $money->balance+$params['money'];
                DB::connection('lovbee')->table('shops_deposits')->where('user_id', $params['user_id'])->update(array_merge($data, ['updated_at'=>$time]));
                $data['deposit_id'] = $money->id;
            }

            $data['created_at'] = $data['created_at'] ?? $time;
            $data['money'] = $params['money'];
            DB::connection('lovbee')->table('shops_deposits_logs')->insert(array_merge($data, $base));

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Transaction update:', ['code'=>$exception->getCode(), 'message'=>$exception->getMessage()]);
        }

        return response()->json(['result'=>'success']);
    }

    public function money(Request $request, $id)
    {
        $params = $request->all();
        $logs = DB::connection('lovbee')->table('shops_deposits_logs')->where('user_id', $id)->orderByDesc('id')->paginate(10);

        $params['result'] = $logs;
        return view('backstage.business.deposit.money', $params);
    }

}
