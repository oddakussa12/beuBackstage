<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    //
    use Translatable;

    protected $table = 'translations';

    protected $primaryKey = "translation_id";

    const CREATED_AT = 'translation_created_at';

    const UPDATED_AT = 'translation_updated_at';

    const DELETED_AT = 'translation_deleted_at';

    public $translationModel = 'App\Models\TranslationTranslation';

    protected $localeKey = 'translation_locale';

    public $translatedAttributes = ['translation_value'];

    public $paginateParamName = 'translation_page';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'translation_key',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
