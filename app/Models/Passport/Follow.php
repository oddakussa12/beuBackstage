<?php

namespace App\Models\Passport;

use App\Models\Base;

class Follow extends Base
{

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'common_follows';

    const CREATED_AT = 'user_created_at';

    const UPDATED_AT = 'user_updated_at';

    const DELETED_AT = 'user_deleted_at';

    protected $guarded = ['user_id' ,'followable_id' ,'followable_type' ,'relation' ,'created_at' ,'updated_at'];

    protected $fillable = [
        'user_id' ,
        'followable_id' ,
        'followable_type' ,
        'relation' ,
        'created_at' ,
        'updated_at' ,
    ];



}
