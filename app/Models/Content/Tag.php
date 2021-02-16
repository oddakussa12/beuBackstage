<?php

namespace App\Models\Content;

use App\Models\Base;
use Dimsav\Translatable\Translatable;

class Tag extends Base
{
    use Translatable;

    protected $table = "tags";

    const CREATED_AT = 'tag_created_at';

    const UPDATED_AT = 'tag_updated_at';

    protected $primaryKey = 'tag_id';

    public $translatedAttributes = ['tag_locale' , 'tag_name'];

    protected $fillable = ['tag_id' , 'tag_sort' , 'tag_status'];

    public $translationModel = 'App\Models\Content\TagTranslation';

    public $paginateParamName = 'tag_page';

    protected $localeKey = 'tag_locale';
}
