<?php

namespace App\Models\Passport;

use App\Models\Base;

class Group extends Base
{
	protected $table = "groups";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';


    protected $fillable = [
        'id' ,
        'name' ,
        'avatar' ,
        'member' ,
        'administrator' ,
        'user_id' ,
        'created_at' ,
        'updated_at',
    ];
}
