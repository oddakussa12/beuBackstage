<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;


class Goods extends Model
{
    protected $table = "goods";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'id' => 'string',
        'shop_id' => 'string',
        'user_id' => 'string',
        'image'=>'array'
    ];

    protected $fillable = ['user_id' , 'shop_id' , 'name' , 'image' , 'like' , 'price', 'recommend', 'recommended_at', 'description', 'status'];

    public function setRecommendAttribute($value)
    {
        $value = strval($value);
        if($value=='off')
        {
            $this->attributes['recommend'] = 0;
        }else{
            $this->attributes['recommend'] = 1;
        }
    }

    public function getImageAttribute($value)
    {
        $value = strval($value);
        $value = \json_decode($value , true);
        if(is_array($value))
        {
            return $value;
        }
        return array();
    }

}
