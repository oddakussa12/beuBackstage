<?php

namespace App\Models\Content;

use App\Models\Base;
use App\Models\Passport\User;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Base
{
    use Translatable,SoftDeletes;

	protected $table = "posts_comments";

    const CREATED_AT = 'comment_created_at';

    const UPDATED_AT = 'comment_updated_at';

    const DELETED_AT = 'comment_deleted_at';

    protected $primaryKey = 'comment_id';

    public $translatedAttributes = ['comment_locale' , 'comment_content'];

    protected $fillable = [
        'post_id' ,
        'user_id' ,
        'comment_to_id' ,
        'comment_comment_p_id' ,
        'comment_top_id' ,
        'comment_default_locale' ,
    ];

    protected $casts = [
        'comment_image' => 'array',
    ];

    public $translationModel = PostCommentTranslation::class;

    protected $localeKey = 'comment_locale';

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id' , 'user_id')->withDefault();
    }

    public function commentedOwner()
    {
        return $this->belongsTo(User::class, 'comment_to_id' , 'user_id')->withDefault();
    }


}
