<?php

namespace App\Models;


class Medal extends Base
{
    protected $table = 'medals';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'desc', 'image', 'category'];

}
