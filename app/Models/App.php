<?php

namespace App\Models;


class App extends Base
{

    protected $table = "app_versions";
    
    protected $fillable = ['platform' , 'type' , 'version' , 'apk_url' , 'upgrade_point' , 'status'];

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $casts = ['status' => 'boolean'];

    public $paginateParamName = 'country_page';

    public function setUpdatedAtAttribute($value) {
        // Do nothing.
    }

    public function getPlatformAttribute($value)
    {
        if($value==0)
        {
            return 'IOS';
        }
        return 'Android';
    }

    public function setUpgradePointAttribute($value)
    {
        $value = strval($value);
        $this->attributes['upgrade_point'] = trim($value);
    }

    public function setUpgradeTypeAttribute($value)
    {
        $value = strval($value);
        if($value=='off')
        {
            $this->attributes['upgrade_type'] = 0;
        }else{
            $this->attributes['upgrade_type'] = 1;
        }
    }

}
