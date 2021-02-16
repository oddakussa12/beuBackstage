<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $table = "operates";

    const CREATED_AT = 'operate_created_at';
    const UPDATED_AT = 'operate_updated_at';

    protected $fillable = ['operate_uid','operate_username','operate_type','operate_ip','operate_content'];
}
