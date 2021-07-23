<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $connection = 'lovbee';

    protected $table = "orders";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = ['user_id', 'shop_id' , 'user_name' , 'user_contact' , 'user_address' , 'detail' , 'address', 'order_price' , 'currency'];

    protected $appends = ['format_price'];

    protected $casts = [
        'user_id' => 'string',
        'shop_id' => 'string',
        'detail' => 'array',
        'order_price' => 'float',
        'shop_price' => 'float',
        'delivery_coast' => 'float',
        'reduction' => 'float',
        'discount' => 'float',
        'discounted_price' => 'float',
        'deposit' => 'float',
        'brokerage_percentage' => 'float',
        'brokerage' => 'float',
        'profit' => 'float',
        'promo_price' => 'float',
        'total_price' => 'float',
    ];

    public function getFormatPriceAttribute()
    {
        return sprintf("%1\$.2f", $this->order_price).' '. $this->currency;
    }


}
