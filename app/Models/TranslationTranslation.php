<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationTranslation extends Model
{

    protected $primaryKey = 'translation_content_id';
    
    protected $fillable = ['translation_id', 'translation_value'];

    protected $table = 'translations_translations';

    public $timestamps = false;
    
}
