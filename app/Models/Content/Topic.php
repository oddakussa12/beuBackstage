<?php

namespace App\Models\Content;

use App\Models\Base;
use App\Models\Report\Report;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Guard;

class Topic extends Base
{

    protected $table = "hot_topics";

    protected $dateFormat = 'U';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
    //const DELETED_AT = 'is_delete';


    protected $primaryKey = 'id';
    protected $fillable = [
        'topic_content' ,
        'flag' ,
        'sort' ,
        'start_time' ,
        'end_time',
        'is_delete'
    ];
}
