<?php

namespace App\Models;


use Dimsav\Translatable\Translatable;

class Video extends Base
{
    use Translatable;
//    use TransLatableSelf;
    protected $connection = 'mt_front';

    const CREATED_AT = 'video_created_at';

    const UPDATED_AT = 'video_updated_at';

    protected $primaryKey = 'video_id';

    public $translatedAttributes = ['video_title' , 'video_summary' , 'video_keyword'];

    protected $fillable = ['video_uuid' , 'video_cover' , 'video_url' , 'video_class_id' , 'video_producer_id' , 'video_producer' , 'video_is_auth'];

    public $translationModel = 'App\Models\VideoTranslation';

    protected $localeKey = 'video_locale';

}
