<?php

namespace App\Models\Content;

use App\Models\Base;

class Event extends Base
{

	protected $table = "events";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    protected $dateFormat = 'U';


    protected $fillable = [
        'type' ,
        'name' ,
        'image' ,
        'value' ,
        'sort' ,
        'status' ,
        'flag' ,
        'created_at' ,
    ];


}
