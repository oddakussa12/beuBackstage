<?php

namespace App\Models\Content;

use App\Models\Base;

class VideoTranslation extends Base
{
    const CREATED_AT = 'video_created_at';

    const UPDATED_AT = 'video_updated_at';

    protected $primaryKey = 'video_translation_id';

    protected $casts = ['video_status' => 'boolean' , 'video_is_examin' => 'boolean'];

    protected $fillable = ['video_id', 'video_title', 'video_summary', 'video_keyword', 'video_locale', 'video_status', 'video_is_examin'];

    protected $table = 'videos_translations';
}
