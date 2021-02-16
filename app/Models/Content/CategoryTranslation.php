<?php

namespace App\Models\Content;

use App\Models\Base;

class CategoryTranslation extends Base
{
	public $timestamps = false;

    protected $primaryKey = 'category_translation_id';

    protected $fillable = ['category_translation_id','category_locale' , 'category_name'];

    protected $table = 'categories_translations';
}
