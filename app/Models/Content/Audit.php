<?php

namespace App\Models\Content;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Audit extends Authenticatable
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $table = 'audits';

    protected $primaryKey = "id";

    protected $dateFormat = 'U';

    protected $fillable = [
        'admin_id' , 'post_id', 'post_uuid' ,'post_audit', 'admin_user_name', 'source', 'user_id', 'is_delete', 'unoperator'
    ];



}
