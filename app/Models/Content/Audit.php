<?php

namespace App\Models\Content;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Audit extends Authenticatable
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $table = 'audits';

    protected $primaryKey = "id";

    protected $fillable = ['admin_id', 'admin_user_name', 'user_id', 'post_id', 'source', 'audited'];



}
