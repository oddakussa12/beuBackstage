<?php

namespace App\Models\Content;

use App\Models\Base;

class PostTranslation extends Base
{
    const CREATED_AT = 'post_translation_created_at';

    const UPDATED_AT = 'post_translation_updated_at';

    protected $primaryKey = 'post_translation_id';

//    protected $casts = ['post_status' => 'boolean' , 'post_is_examin' => 'boolean'];

    protected $fillable = ['post_id', 'post_title', 'post_locale', 'post_content'];

    protected $table = 'posts_translations';
}
