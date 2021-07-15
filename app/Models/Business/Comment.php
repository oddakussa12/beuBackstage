<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class Comment extends Model
{
    protected $connection = 'lovbee';

    protected $table = "comments";

    protected $primaryKey = "comment_id";

    public $incrementing = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = ['goods_id', 'owner' , 'user_id' , 'to_id' , 'p_id' , 'top_id' , 'content', 'step', 'level' , 'child_comment' , 'service' , 'quality', 'point', 'media' , 'type' , 'reviewer' , 'verified' , 'verified_at'];

    protected $hidden = ['to_id' , 'top_id' , 'step' , 'level' , 'service' , 'quality' , 'type' , 'reviewer' , 'verified' , 'verified_at' , 'updated_at'];

    protected $casts = [
        'top_id' => 'string',
        'to_id' => 'string',
        'p_id' => 'string',
        'user_id' => 'string',
        'owner' => 'string',
        'comment_id' => 'string',
        'media'=> 'array'
    ];

    public function setLevelAttribute($value)
    {
        if($value=='on')
        {
            $this->attributes['level'] = 1;
        }else{
            $this->attributes['level'] = 0;
        }
    }
}
