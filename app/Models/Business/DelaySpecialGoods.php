<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class DelaySpecialGoods extends Model
{
    protected $table = "delay_special_goods";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'id' => 'string',
        'delay_id' => 'string',
        'shop_id' => 'string',
        'goods_id' => 'string',
        'special_price'=>'float',
        'packaging_cost'=>'float'
    ];

    protected $fillable = ['delay_id' , 'shop_id' , 'goods_id' , 'special_price' , 'packaging_cost' , 'start_time' , 'deadline'];
}
