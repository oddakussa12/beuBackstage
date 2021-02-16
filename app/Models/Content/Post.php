<?php

namespace App\Models\Content;

use App\Models\Base;
use App\Models\Report\Report;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Base
{
    use Translatable,SoftDeletes;

	protected $table = "posts";

    const CREATED_AT = 'post_created_at';

    const UPDATED_AT = 'post_updated_at';

    const DELETED_AT = 'post_deleted_at';

    protected $primaryKey = 'post_id';

    public $translatedAttributes = ['post_locale','post_title' , 'post_content'];

    protected $fillable = [
        'post_uuid' ,
        'user_id' ,
        'post_category_id' ,
        'post_media' ,
        'post_content_default_locale' ,
        'post_default_locale' ,
        'post_country_id' ,
        'post_hotting' ,
        'post_fine' ,
        'post_topping' ,
        'post_like_num' ,
        'post_comment_num',
        'post_topped_at',
    ];

    public $translationModel = 'App\Models\Content\PostTranslation';

    protected $localeKey = 'post_locale';

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function setPostHottingAttribute($value)
    {
        $this->attributes['post_hotting'] = strtolower($value)=='on'?1:0;
    }

    public function setPostFineAttribute($value)
    {
        $this->attributes['post_fine'] = strtolower($value)=='on'?1:0;
    }
    public function setPostToppingAttribute($value)
    {
        $this->attributes['post_topping'] = strtolower($value)=='on'?1:0;
    }

    public function getPostIndexTitleAttribute()
    {
        $title = $this->post_decode_title;
        if(empty($title))
        {
            return str_limit(strip_tags($this->post_decode_content) , 90 , '...');
        }
        return $title;
    }

    public function getPostDecodeTitleAttribute()
    {
        if(empty($this->post_title))
        {
            $post_title = optional($this->translate(config('translatable.translate_default_lang')))->post_title;
            if(empty($post_title))
            {
                $post_title = optional($this->translate($this->post_default_locale))->post_title;
                if(empty($post_title))
                {
                    $post_title = optional($this->translate('en'))->post_title;
                }
            }
        }else{
            $post_title = $this->post_title;
        }
        return htmlspecialchars_decode(htmlspecialchars_decode($post_title , ENT_QUOTES) , ENT_QUOTES);
    }

    public function getPostDecodeContentAttribute()
    {
        if(empty($this->post_content))
        {
            $post_content = optional($this->translate(config('translatable.translate_default_lang')))->post_content;
            if(empty($post_content))
            {
                $post_content = optional($this->translate($this->post_content_default_locale))->post_content;
                if(empty($post_content))
                {
                    $post_content = optional($this->translate('en'))->post_content;
                }
            }
        }else{
            $post_content = $this->post_content;
        }
        return htmlspecialchars_decode(htmlspecialchars_decode($post_content , ENT_QUOTES) , ENT_QUOTES);
    }
}
