<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Props extends Model
{
    //
    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $fillable = [
      'name', 'cover', 'url', 'category', 'camera', 'is_default', 'recommendation', 'is_delete'
    ];

}
