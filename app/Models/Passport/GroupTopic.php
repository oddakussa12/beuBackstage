<?php

namespace App\Models\Passport;

use App\Models\Base;

class GroupTopic extends Base
{
	protected $table = "groups_topics";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';


    protected $fillable = [
        'id' ,
        'group_id' ,
        'topic_id' ,
        'topic_name' ,
        'created_at' ,
        'created_by',
        'updated_at',
        'updated_by',
    ];

}
