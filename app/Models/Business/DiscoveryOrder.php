<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class DiscoveryOrder extends Model
{
    protected $connection = 'lovbee';

    protected $table = "delivery_orders";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = ['status' , 'menu' , 'shop_price' , 'operator' , 'comment'];

    protected $appends = ['format_price'];

    protected $casts = [
        'user_id' => 'string',
        'shop_id' => 'string',
        'detail' => 'array',
        'order_price' => 'float',
        'free_delivery' => 'boolean',
    ];

    public function getFormatPriceAttribute()
    {
        return sprintf("%1\$.2f", $this->order_price).' '. $this->currency;
    }


}
