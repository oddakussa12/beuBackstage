<?php

namespace App\Models\Content;

use App\Models\Base;

class VideoView extends Base
{
    //
    const CREATED_AT = 'video_view_created_at';

    protected $primaryKey = 'video_view_id';

    protected $fillable = ['video_view_user_id','video_view_video_id'];

    protected $table = 'videos_views';
//
//    public function user()
//    {
//        return $this->belongsTo('App\Models\Passport\User' , 'video_id');
//    }
}
