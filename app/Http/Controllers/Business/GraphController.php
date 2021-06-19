<?php

namespace App\Http\Controllers\Business;

use App\Models\Passport\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class GraphController extends Controller
{
    protected $status = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];


    public function index()
    {
        $type = ['orderStatus'];
        foreach ($type as $item) {
            $result[$item] = $this->$item();
        }

        return view('backstage.business.graph.index', compact('result'));
    }
    // 购物车商品统计
    public function cartGoods()
    {
        $result   = DB::connection('lovbee')->table('shopping_carts')->select(DB::raw('count(*) num, goods_id'))->groupBy('goods_id')->orderByDesc('num')->paginate(10);
        $goodsIds = $result->pluck('goods_id')->toArray();
        $goods    = DB::connection('lovbee')->table('shopping_carts')->whereIn('id', $goodsIds)->get();
        foreach ($result as $item) {
            foreach ($goods as $good) {
                if ($item->goods_id==$good->id) {
                    $item->goods_name  = $good->name;
                    $item->goods_image = json_decode($good->image, true);
                }
            }
        }
        return $result;
    }

    // 加入购物车最多的店铺统计
    public function cartShops()
    {
        return $this->users('shop_id');
    }
    // 加入购物车最多的用户统计
    public function cartUsers()
    {
        return $this->users('user_id');
    }

    public function users($keyword)
    {
        $result  = DB::connection('lovbee')->table('shopping_carts')->select(DB::raw('count(*) num'), $keyword)->groupBy($keyword)->orderByDesc('num')->paginate(10);
        $ids = $result->pluck($keyword)->toArray();
        $users   = User::whereIn('user_id', $ids)->get();
        foreach ($result as $item) {
            foreach ($users as $user) {
                if ($item->$keyword==$user->user_id) {
                    $item->nick_name   = $user->user_nick_name;
                    $item->name        = $user->user_name;
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
            'tooltip' => ['trigger'=>'item', 'formatter'=>'{b} : {c} <br>  {d}%'],
            'legend'  => ['orient'=>$legendOrient, 'left'=>$legendLeft], // 'orient'=>vertical/ horizontal
            'series'  => [
                [
                    'type'   => 'pie',
                    'radius' => '50%',
                    'data'   => $result ?? [],
                    'label'  => ['normal'=>['formatter'=>'{b} : {c} '.PHP_EOL.' {d}%']]
                ]
            ]
        ];
    }

    // 订单状态统计
    public function orderStatus()
    {
        $data   = [];
        $table  = 'orders';
        $table  = 'delivery_orders';
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

    public function test()
    {

    }
}
