<?php

namespace App\Models;

use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $table = "posts_comments";

    protected $connection = 'lovbee';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'comment_id';

    protected $hidden = [
        'updated_at',
    ];

    protected $fillable = [
        'comment_id',
        'post_id',
        'user_id',
        'to_id',
        'p_id',
        'top_id',
        'content',
        'child_comment'
    ];

    public $paginateParamName = 'page';
    
    public $perPage = 10;

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id' , 'user_id')->select('user_id', 'user_nick_name', 'user_avatar', 'user_level')->withDefault();
    }

}
