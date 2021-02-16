<?php

namespace App\Models\Content;

use App\Models\Base;

class Banner extends Base
{

	protected $table = "banners";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $dateFormat = 'U';


    protected $fillable = [
        'type' ,
        'image' ,
        'value' ,
        'sort' ,
        'status' ,
        'repeat' ,
        'created_at' ,
    ];


}
