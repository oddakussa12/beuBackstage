<?php
namespace App\Exports;

use Carbon\Carbon;
use App\Models\Passport\User;
use App\Models\Business\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class OrderExport extends StringValueBinder implements FromCollection,WithHeadings,ShouldAutoSize,WithCustomValueBinder
{
    use Exportable;

    private $params;
    private $date_time;
    protected $status   = ['InProcess', 'Completed', 'Canceled'];

    protected $schedules = ['1'=>'Ordered', '2'=>'ConfirmOrder', '3'=>'CallDriver', '4'=>'ContactedShop', '5'=>'Delivered', '6'=>'NoResponse', '7'=>'JunkOrder', '8'=>'UserCancelOrder', '9'=>'ShopCancelOrder', '10'=>'Other'];


    public function __construct($params , $date_time)
    {
        $this->params = $params;
        $this->date_time = $date_time;
    }

    /**
     * @return string[]
     * 设置header头
     */
    public function headings(): array
    {
        return [
            'OrderId', 'OrderStatus', 'ShopName',
            'ShopContact', 'ShopAddress','UserName',
            'UserContact','UserAddress','OrderSchedule',
            'PromoCode','DeliveryCost','DiscountType' ,
            'Reduction' ,'TimeConsuming' , 'Discount' ,
            'FreeDelivery' , 'Mark' , 'Goods' ,
            'OrderPrice' , 'PromoPrice' , 'DiscountedPrice' ,
            'DeliveredAt' , 'CreatedAt'
        ];
    }

    /**
     * @return Collection
     * 返回结果集
     */
    public function collection(): Collection
    {
        $params = $this->params;
        $promoCode = $params['promo_code']??'';
        $schedule = $params['schedule']??0;
        $date_time = $this->date_time;
        $shopId = (int)($params['user_id']??0);
        $ordersWhere = new Order();
        if(empty($promoCode))
        {
            if (isset($params['status'])) {
                $status = (int)$params['status'];
                $ordersWhere = $ordersWhere->where('status', $status);
            }
            if (!empty($schedule)) {
                $ordersWhere = $ordersWhere->where('schedule', $schedule);
            }
            $shopId!==0  && $ordersWhere = $ordersWhere->where('shop_id', $shopId);
            if($date_time!==false)
            {
                $ordersWhere = $ordersWhere->whereBetween('created_at' , array($date_time['start'] , $date_time['end']));
            }
        }else{
            $ordersWhere = $ordersWhere->where('status', 1)->where('promo_code', $promoCode);
        }
        $orders   = $ordersWhere->orderByDesc('created_at')->get();
        $shopIds = $orders->pluck('shop_id')->unique()->toArray();
        $shops = User::whereIn('user_id' , $shopIds)->get();
        $time = Carbon::now()->subHour(8)->toDateTimeString();
        $orders->each(function($order) use ($shops , $time){
            $order->shop = $shops->where('user_id' , $order->shop_id)->first();
            $duration = strtotime($time)-strtotime($order->created_at);
            if (($order->schedule===1 && $duration>300) || ($order->schedule===2 && $duration>600) || ($order->schedule===3 && $duration>780) || ($order->schedule===4 && $duration>3600)) {
                $order->color = 1;
            }
        });
        $orders = $orders->toArray();
        $data = array();
        /**
         * 'OrderId', 'OrderStatus', 'ShopName',
        'ShopContact', 'ShopAddress','UserName',
        'UserContact','UserAddress','OrderSchedule',
        'PromoCode','DeliveryCost','DiscountType' ,
        'Reduction' ,'TimeConsuming' , 'Discount' ,
        'FreeDelivery' , 'Mark' , 'Goods' ,
        'OrderPrice' , 'PromoPrice' , 'DiscountedPrice' ,
        'DeliveredAt' , 'CreatedAt'
         */
        foreach ($orders as $order)
        {
            $goods = '';
            $detail = $order['detail'];
            foreach ($detail as $d)
            {
                $goods .= $d['name']."|";
            }
            array_push($data , array(
                "OrderId" => $order['order_id'],
                "OrderStatus" => $this->status[$order['status']],
                'shopName'=>$order['shop']->user_nick_name,
                'ShopContact'=>$order['shop']->user_contact,
                'ShopAddress'=>$order['shop']->user_address,
                'UserName'=>$order['user_name'],
                'UserContact'=>$order['user_contact'],
                'UserAddress'=>$order['user_address'],
                'OrderSchedule'=>$this->schedules[$order['schedule']],
                'PromoCode'=>$order['promo_code'],
                'DeliveryCost'=>empty($order['delivery_coast'])?'0':$order['delivery_coast'],
                'DiscountType'=>$order['discount_type'],
                'Reduction'=>$order['reduction'],
                'TimeConsuming'=>'0',
                'Discount'=>empty($order['discount'])?'0':$order['discount'],
                'FreeDelivery'=>empty($order['free_delivery'])?'0':1,
                'Mark'=>$order['comment'],
                'Goods'=>$goods,
                'OrderPrice'=>$order['order_price'],
                'PromoPrice'=>$order['promo_price'],
                'DiscountedPrice'=>empty($order['discounted_price'])?'0':$order['discounted_price'],
                'DeliveredAt'=>$order['delivered_at'],
                'CreatedAt'=>$order['created_at']
            ));
        }
        return collect($data);
    }

}