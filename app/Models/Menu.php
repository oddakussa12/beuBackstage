<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    use Translatable;

    protected $table = 'menus';

    protected $primaryKey = "menu_id";

    const CREATED_AT = 'menu_created_at';

    const UPDATED_AT = 'menu_updated_at';

    const DELETED_AT = 'menu_deleted_at';

    public $translationModel = 'App\Models\MenuTranslation';

    protected $localeKey = 'menu_locale';

    public $translatedAttributes = ['menu_name'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_p_id', 'menu_name', 'menu_icon','menu_url','menu_auth','menu_icon',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

}
