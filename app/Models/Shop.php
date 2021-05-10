<?php

namespace App\Models;

use App\Models\Passport\User;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = "shops";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $casts = [
        'image' => 'array',
    ];

    protected $fillable = ['user_id', 'name' , 'avatar' , 'cover' , 'recommend' , 'nick_name' , 'address', 'phone', 'description'];

}
