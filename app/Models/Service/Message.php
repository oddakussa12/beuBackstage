<?php

namespace App\Models\Service;

use App\Models\Base;

class Message extends Base
{
    protected $table = "service_messages";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $dateFormat = "U";

    protected $fillable = [
        'type',
        'value',
        'title',
        'content',
        'image'
    ];
}
