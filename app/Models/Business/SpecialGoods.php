<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class SpecialGoods extends Model
{
    protected $table = "special_goods";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'id' => 'string',
        'shop_id' => 'string',
        'goods_id' => 'string',
        'special_price'=>'float',
        'packaging_cost'=>'float'
    ];

    protected $fillable = ['shop_id' , 'goods_id' , 'special_price' , 'packaging_cost' , 'deadline'];
}
