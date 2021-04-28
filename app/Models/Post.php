<?php

namespace App\Models;

use App\Models\Passport\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'post_id';

    protected $casts = [
        'post_id' => 'string',
        'ext' => 'array',
    ];

    protected $fillable = [
        'post_id',
        'user_id',
        'type',
        'image',
        'video',
        'ext',
        'like',
        'comment'
    ];

    public function comments()
    {
        return $this->hasMany(PostComment::class , 'post_id' , 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id' , 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id' , 'user_id')->withDefault();
    }

    public function getPostOwnerAttribute()
    {
        if (auth()->check()) {
            return $this->ownedBy(auth()->user());
        }
        return false;
    }

    public function getPostLikeStateAttribute()
    {
        if(auth()->check())
        {
            $likeBy = $this->isLikedBy(auth()->user());
            return empty($likeBy)?false:true;
        }
        return false;
    }

    public function getFormatCreatedAtAttribute()
    {
        return dateTrans($this->created_at);
    }

}
