<?php

namespace App\Models\Report;

use App\Models\Base;
use Illuminate\Database\Eloquent\Builder;

class Report extends Base
{

    protected $table = "reports";

    protected $fillable = ['user_id' ,  'reportable_id' , 'reportable_type'];

    public $paginateParamName = 'report_page';

    protected $primaryKey = 'id';


    public function reportable()
    {
        return $this->morphTo();
    }

    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('dislikable_type', app($type)->getMorphClass());
    }
    
}
