<?php

namespace App\Models\Passport;

use Carbon\Carbon;
use App\Models\Base;
use App\Models\Report\Report;

class User extends Base
{
    protected $primaryKey = 'user_id';

    const CREATED_AT = 'user_created_at';
    const UPDATED_AT = 'user_updated_at';
    const DELETED_AT = 'user_deleted_at';

    protected $guarded = ['user_name' , 'users_email' , 'user_ip_address' , 'user_active_time' , 'user_last_active' , 'user_registered' , 'user_active_expire'];

    protected $fillable = [
        'user_name' ,
        'users_email' ,
        'user_ip_address' ,
        'user_first_name' ,
        'user_last_name' ,
        'user_gender' ,
        'user_email_code' ,
        'user_device_id' ,
        'user_language' ,
        'user_avatar' ,
        'user_cover' ,
        'user_profile_like_num' ,
        'user_src' ,
        'user_country_id' ,
        'user_age' ,
        'user_about' ,
        'user_google' ,
        'user_facebook' ,
        'user_twitter' ,
        'user_instagram' ,
        'user_active' ,
        'user_verified' ,
        'user_is_pro' ,
        'user_level' ,
        'user_age_changed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_pwd'
    ];

    public function videoViews()
    {
        return $this->hasMany('App\Models\Content\VideoView' , 'video_view_user_id' , 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id', $this->getKeyName());
    }

    public function setUserLevelAttribute($value)
    {
        $this->attributes['user_level'] = strtolower($value)=='on'?1:0;
    }

    public function getUserCountryAttribute()
    {
        $countries = config('country');
        $country = ($this->user_country_id-1);
        if(array_key_exists($country , $countries))
        {
            return strtolower($countries[$country]['name']);
        }
        return $country;
    }
//
//    public function getUserCreatedAtAttribute()
//    {
//        return Carbon::parse($this->user_created_at)->addHours(8)->toDateTimeString();
//    }
}
