<?php

namespace App\Models\Report;


use App\Models\Base;

class Feedback extends Base
{

    protected $table = "feedback";

    const CREATED_AT = 'feedback_created_at';
    
    const UPDATED_AT = 'feedback_updated_at';

    protected $primaryKey = 'feedback_id';

    protected $fillable = ['feedback_id' , 'feedback_name' , 'feedback_email','feedback_content','feedback_result','feedback_created_at','feedback_deleted_at'];

    public $paginateParamName = 'feedback_page';



    public function setFeedbackResultAttribute($value)
    {
        $this->attributes['feedback_result'] = strtolower($value)=='on'?1:0;
    }

}
