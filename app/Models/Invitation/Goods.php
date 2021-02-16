<?php

namespace App\Models\Invitation;

use App\Models\Base;

class Goods extends Base
{

    protected $table = "goods";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'type',
        'description',
        'image',
        'price',
        'country',
        'score',
        'limiting',
        'start_time',
        'end_time',
        'total',
        'status',
        'is_delete',
    ];
}
