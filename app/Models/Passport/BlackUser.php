<?php

namespace App\Models\Passport;

use App\Models\Base;

class BlackUser extends Base
{

    protected $table = "black_users";

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $dateFormat = "U";


    protected $fillable = [
        'id',
        'user_id',
        'desc',
        'start_time',
        'end_time',
        'operator',
        'unoperator',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}
