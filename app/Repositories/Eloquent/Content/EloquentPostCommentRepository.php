<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent\Content;

use Carbon\Carbon;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\Content\PostRepository;
use App\Repositories\Contracts\Content\PostCommentRepository;

class EloquentPostCommentRepository  extends EloquentBaseRepository implements PostCommentRepository
{


    /**
     * @inheritdoc
     */
    public function paginate($perPage = 10, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        $postId = request()->get('post_id' , '');
        $comment = $this->model->withTrashed();
        if(!empty($postId))
        {
            $comment = $comment->where('post_id' , $postId);
        }
        return $comment->with(['translations'=>function($query){
            $query->where('post_locale' , locale());
        }])->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
    }

    public function findByPostId($postId)
    {
        $params = request()->all();
        $comment = $this->model->withTrashed();
        $now = Carbon::now();
        if(!empty($params['user_name']))
        {
            $user = app(UserRepository::class)->findByAttributes(array('user_name'=>$params['user_name']));
            $userId = empty($user)?0:$user->user_id;
            $comment = $comment->where('user_id' , $userId);
        }
        if(!empty($postId)&&empty($params['post_uuid']))
        {
            $comment = $comment->where('post_id' , $postId);
        }else{
            if(!empty($params['post_uuid']))
            {
                $post = app(PostRepository::class)->findByUuid($params['post_uuid']);
                $postId = $post->post_id;
                $comment = $comment->where('post_id' , $postId);
            }

        }

        if(!empty($params['dateTime']))
        {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            if($end>$endDate)
            {
                $end =  $endDate;
            }
            if($start>$end)
            {
                $start = $end;
            }
            $comment = $comment->where('comment_created_at' , '>=' , $start)->where('comment_created_at' , '<=' , $end);
        }
        return $comment->with(['translations'=>function($query){
            $query->where('comment_locale' , locale());
        }])->with('owner')->with('commentedOwner')->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate(10);
    }



}
