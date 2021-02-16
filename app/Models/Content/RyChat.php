<?php

namespace App\Models\Content;

use App\Models\Base;
use Illuminate\Database\Eloquent\SoftDeletes;

class RyChat extends Base
{

//    use SoftDeletes;

    protected $table = "ry_chats";

    const CREATED_AT = 'chat_created_at';

    const UPDATED_AT = 'chat_updated_at';

    const DELETED_AT = 'chat_deleted_at';

    protected $primaryKey = 'chat_id';

    protected $fillable = [

    ];




}
