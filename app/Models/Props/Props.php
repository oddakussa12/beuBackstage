<?php

namespace App\Models\Props;

use App\Models\Base;

class Props extends Base
{
    //
    protected $connection = 'lovbee';

    protected $table = 'props';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $fillable = [
      'name', 'cover', 'remark', 'url', 'category', 'hash', 'camera', 'is_default', 'recommendation', 'is_delete'
    ];

}
