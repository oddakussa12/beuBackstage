<?php

namespace App\Models\Props;

use App\Models\Base;

class PropsCategory extends Base
{
    //
    protected $connection = 'lovbee';

    protected $table = 'props_categories';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $fillable = [
      'name', 'language', 'is_delete', 'sort',
    ];

}
