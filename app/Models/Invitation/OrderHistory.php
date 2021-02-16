<?php

namespace App\Models\Invitation;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{

    protected $table = "order_history";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'order_id',
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
        'operator',
    ];

}
