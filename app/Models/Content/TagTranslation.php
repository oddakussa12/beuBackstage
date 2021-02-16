<?php

namespace App\Models\Content;

use App\Models\Base;

class TagTranslation extends Base
{
    public $timestamps = false;

    protected $primaryKey = 'tag_translation_id';

    protected $fillable = ['tag_translation_id','tag_locale' , 'tag_name'];

    protected $table = 'tags_translations';
}
