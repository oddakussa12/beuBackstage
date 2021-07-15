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
        'image'=>'array',
        'price'=>'float',
        'quality'=>'float',
        'service'=>'float',
    ];

    protected $fillable = ['user_id' , 'shop_id' , 'name' , 'image' , 'like' , 'price', 'recommend', 'recommended_at', 'description', 'status'];

    protected $appends = ['format_price' , 'average_point'];

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

    public function getFormatPriceAttribute()
    {
        return sprintf("%1\$.2f", $this->price).' '. $this->currency;
    }

    public function getAveragePointAttribute()
    {
        if(empty($this->comment))
        {
            return 0;
        }
        return round($this->point/$this->comment , 1);
    }

}
