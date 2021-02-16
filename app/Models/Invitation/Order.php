<?php

namespace App\Models\Invitation;

use App\Models\Base;

class Order extends Base
{

    protected $table = "orders";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'good_id',
        'user_id',
        'price',
        'score',
        'num',
        'good_name',
        'good_image',
        'user_name',
        'user_real_name',
        'country',
        'phone',
        'status',
    ];

}
