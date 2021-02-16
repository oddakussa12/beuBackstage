<?php

namespace App\Models\Content;

use App\Models\Base;

class PostCommentTranslation extends Base
{

    protected $primaryKey = 'comment_translation_id';

//    protected $casts = ['post_status' => 'boolean' , 'post_is_examin' => 'boolean'];

    protected $fillable = ['comment_id', 'comment_locale', 'comment_content'];

    protected $table = 'posts_comments_translations';
}
