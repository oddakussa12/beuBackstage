<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class ShopTagTranslation extends Model
{
    protected $connection = 'lovbee';

    protected $table = "shops_tags_translations";

    protected $primaryKey = "id";

    public $incrementing = false;

    protected $fillable = ['locale' , 'tag_content'];

    protected $hidden = ['id' , 'tag_id'];


    const UPDATED_AT = null;

    const CREATED_AT = null;


}
