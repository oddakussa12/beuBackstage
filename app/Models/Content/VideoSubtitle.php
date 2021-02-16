<?php

namespace App\Models\Content;

use App\Models\Base;

class VideoSubtitle extends Base
{
	const CREATED_AT = 'video_subtitle_created_at';

    const UPDATED_AT = 'video_subtitle_updated_at';

    protected $primaryKey = 'video_subtitle_id';

    protected $fillable = ['video_subtitle_id','video_subtitle_locale' , 'video_subtitle_url'];

    protected $table = 'videos_subtitles';
}
