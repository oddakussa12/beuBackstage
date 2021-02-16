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
        'type' ,
        'code' ,
        'name' ,
        'avatar' ,
        'description' ,
        'member_count' ,
        'country_count' ,
        'owner_id' ,
        'is_recommend' ,
        'created_at' ,
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function topic()
    {
        return $this->hasMany(GroupTopic::class , 'group_id' , 'group_id')->select(['topic_name', 'group_id']);
    }
}
