<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable,HasRoles;

    const CREATED_AT = 'admin_created_at';

    const UPDATED_AT = 'admin_updated_at';

    protected $table = 'admins';

    protected $primaryKey = "admin_id";

    protected $guard_name = 'web';

    public $paginateParamName = 'admin_page';

    public $default_password_field = 'admin_password';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_sex' , 'admin_status' ,'admin_username','admin_realname' , 'admin_email' , 'admin_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'admin_password', 'remember_token',
    ];

    public function getAuthPassword() {
        $default_passwrod = $this->default_password_field;
        return $this->$default_passwrod;
    }



}
