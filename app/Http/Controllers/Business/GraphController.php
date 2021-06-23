<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class GraphController extends Controller
{
    protected $status = ['InProcess', 'Completed', 'Canceled'];
    protected $schedule = ['1' => 'Ordered', '2' => 'ConfirmOrder', '3' => 'CallDriver', '4' => 'ContactedShop', '5' => 'Delivered', '6' => 'NoResponse', '7' => 'JunkOrder', '8' => 'UserCancelOrder', '9' => 'ShopCancelOrder', '10' => 'Other'];


    public function index()
    {
        $type = ['orderSchedule', 'orderStatus', 'shopCartByTopShops', 'shopCartByTopGoods', 'OrderByTopShop', 'OrderByTopGoods'];
        foreach ($type as $item) {
            $result[$item] = $this->$item();
        }
        return view('backstage.business.graph.index', compact('result'));
    }

    /**
     * @return \any[]
     * 购物车中的商品排行
     */
    public function shopCartByTopGoods()
    {
        $result = DB::connection('lovbee')->table('shopping_carts')->select(DB::raw('count(*) num, goods_id'))->groupBy('goods_id')->orderByDesc('num')->limit(10)->get();
        $goodsIds = $result->pluck('goods_id')->unique()->toArray();
        $goods = DB::connection('lovbee')->table('goods')->whereIn('id', $goodsIds)->get();
        foreach ($result as $item) {
            foreach ($goods as $good) {
                if ($item->goods_id == $good->id) {
                    $data[] = [
                        'name' => $good->name,
                        'value' => $item->num,
                    ];
                }
            }
        }
        return $this->pie($data ?? [], 'ShopCart-GoodsTop10');
    }

    /**
     * @return \any[]
     * 购物车中的店铺排行
     */
    public function shopCartByTopShops()
    {
        $result = $this->users('user_id');
        $result = collect($result)->toArray();
        $result = $result['data'];

        foreach ($result as $item) {
            $data[] = [
                'name' => $item->nick_name,
                'value' => $item->num,
            ];
        }
        return $this->pie($data ?? [], 'ShopCart-ShopsTop10');
    }



    // 订单状态统计
    public function orderSchedule()
    {
        $data   = [];
        $table  = 'orders';
        $result = DB::connection('lovbee')->table($table)->select(DB::raw('count(*) num, schedule'))->groupBy('schedule')->orderBy('schedule')->get()->toArray();
        foreach ($result as $item) {
            foreach ($this->schedule as $key=>$status) {
                if ($item->schedule==$key) {
                    $data[] = [
                        'name' =>$status,
                        'value'=>$item->num
                    ];
                }
            }
        }
        return $this->pie($data, 'orderSchedule');
    }
    // 订单状态统计
    public function orderStatus()
    {
        $data   = [];
        $table  = 'orders';
        $result = DB::connection('lovbee')->table($table)->select(DB::raw('count(*) num, status'))->groupBy('status')->orderBy('status')->get()->toArray();
        foreach ($result as $item) {
            foreach ($this->status as $key=>$status) {
                if ($item->status==$key) {
                    $data[] = [
                        'name' =>$status,
                        'value'=>$item->num
                    ];
                }
            }
        }
        return $this->pie($data, 'orderStatus');
    }

    /**
     * @return \any[]
     * 订单中商品
     */
    public function OrderByTopGoods()
    {
        $result = DB::connection('lovbee')->table('orders_goods')->select(DB::raw('count(*) value, goods_name name'))->groupBy('goods_id')->orderByDesc('value')->limit(10)->get();
        $result = collect($result)->toArray();
        return $this->pie($result ?? [], 'Order-GoodsTop10');
    }

    /**
     * @return \any[]
     * 订单中商品
     */
    public function OrderByTopShop()
    {
        $result = DB::connection('lovbee')->table('orders_goods')->select(DB::raw('count(*) num, shop_id'))->groupBy('shop_id')->orderByDesc('num')->limit(10)->get();
        $shopIds= $result->pluck('shop_id')->unique()->toArray();
        $shops  = User::whereIn('user_id', $shopIds)->get();

        foreach ($result as $item) {
            foreach ($shops as $shop) {
                if ($shop->user_id==$item->shop_id) {
                    $data[] = [
                        'name'=>$shop->user_nick_name,
                        'value'=>$item->num,
                    ];
                }
            }
        }
        return $this->pie($data ?? [], 'Order-ShopsTop10');
    }
/*
    // 加入购物车最多的店铺统计
    public function cartShops()
    {
        return $this->users('shop_id');
    }
    // 加入购物车最多的用户统计
    public function cartUsers()
    {
        return $this->users('user_id');
    }*/

    public function users($keyword)
    {
        $result  = DB::connection('lovbee')->table('shopping_carts')->select(DB::raw('count(*) num'), $keyword)->groupBy($keyword)->orderByDesc('num')->paginate(10);
        $ids = $result->pluck($keyword)->toArray();
        $users   = User::whereIn('user_id', $ids)->get();
        foreach ($result as $item) {
            foreach ($users as $user) {
                if ($item->$keyword  == $user->user_id) {
                    $item->nick_name = $user->user_nick_name;
                    $item->name = $user->user_name;
                    $item->user_avatar = $user->user_avatar;
                }
            }
        }
        return $result;
    }



    /**
     * @param $result
     * @param string $title
     * @param string $titleLeft
     * @param string $legendOrient
     * @param string $legendLeft
     * @return array
     * 饼状图
     */
    public function pie($result, string $title='', string $titleLeft='center', string $legendOrient='vertical', string $legendLeft='left')
    {
        $result = array_map(function($value) {return (array)$value;}, $result);

        return [
            'title'   => ['text'=>$title, 'left'=>$titleLeft],
            'tooltip' => ['trigger'=>'item', 'formatter'=>'{b} : {c} ({d}%)'],
            'legend'  => ['orient'=>$legendOrient, 'left'=>$legendLeft], // 'orient'=>vertical/ horizontal
            'series'  => [
                [
                    'type'   => 'pie',
                    'radius' => '50%',
                    'data'   => $result ?? [],
                    'label'  => ['normal'=>['formatter'=>'{b} : {c} '.PHP_EOL.' ({d}%)']]
                ]
            ]
        ];
    }

}
