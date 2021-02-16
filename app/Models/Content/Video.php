<?php

namespace App\Models\Content;

use App\Models\Base;
use Dimsav\Translatable\Translatable;

class Video extends Base
{
    use Translatable;

	protected $table = "videos";

    const CREATED_AT = 'video_created_at';

    const UPDATED_AT = 'video_updated_at';

    protected $primaryKey = 'video_id';

    public $translatedAttributes = ['video_locale','video_title' , 'video_summary' , 'video_keyword'];

    protected $fillable = ['video_uuid' , 'video_cover' , 'video_url' , 'video_class_id' , 'video_producer_id' , 'video_producer' , 'video_is_auth' , 'video_title' , 'video_summary' , 'video_keyword'];

    public $translationModel = 'App\Models\Content\VideoTranslation';

    protected $localeKey = 'video_locale';

    public function subtitle()
    {
        return $this->hasMany('App\Models\Content\VideoSubtitle' , 'video_id');
    }
}
