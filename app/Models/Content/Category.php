<?php

namespace App\Models\Content;

use App\Models\Base;
use Dimsav\Translatable\Translatable;

class Category extends Base
{
    use Translatable;

    protected $table = "categories";

    const CREATED_AT = 'category_created_at';

    const UPDATED_AT = 'category_updated_at';

    protected $primaryKey = 'category_id';

    public $translatedAttributes = ['category_locale' , 'category_name'];

    protected $fillable = ['category_id' , 'category_sort' , 'category_status','category_isdel'];

    public $translationModel = 'App\Models\Content\CategoryTranslation';

    public $paginateParamName = 'category_page';

    protected $localeKey = 'category_locale';
    
}
