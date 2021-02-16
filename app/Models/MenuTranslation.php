<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    //
    public $timestamps = false;

    const CREATED_AT = 'menu_created_at';

    const UPDATED_AT = 'menu_updated_at';

    protected $primaryKey = 'menu_content_id';
    
    protected $fillable = ['menu_id', 'menu_name'];

    protected $table = 'menus_translations';

    
}
