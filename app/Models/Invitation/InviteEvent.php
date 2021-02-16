<?php

namespace App\Models\Invitation;

use App\Models\Base;

class InviteEvent extends Base
{

    protected $table = "invitation_events";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'country',
        'activity_status',
        'activity_expire',
        'activity_start_time',
        'activity_end_time',
        'order_status',
        'order_expire',
        'order_start_time',
        'order_end_time',
        'first_register',
        'second',
        'seven',
        'thirty',
        'created_at',
        'updated_at',
    ];

}
