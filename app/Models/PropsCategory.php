<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class PropsCategory extends Model
{
    //
    protected $connection = 'lovbee';

    protected $table = 'props_categories';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $fillable = [
      'name'
    ];

}
