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

class EloquentPostRepository  extends EloquentBaseRepository implements PostRepository
{

    /**
     * @inheritdoc
     */
    public function all()
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->orderBy('video_created_at', 'DESC')->get();
        }
        return $this->model->orderBy('video_created_at', 'DESC')->get();
    }

    public function findByWhere($params)
    {
        $now  = Carbon::now();
        $post = $this->model->withTrashed();
        if (!empty($params['field'])&&!empty($params['v'])) {
            $value = $params['v'];
            $post  = $params['field']=='post_key'?$post->whereHas('translations', function ($query) use ($value) {
                $query->where('post_locale' , locale());
                $query->where('post_content', 'LIKE', "%{$value}%");
            }):$post->where($params['field'] , $params['v']);
        }
        if (!empty($params['user_name'])) {
            $user   = app(UserRepository::class)->findByAttributes(array('user_name'=>$params['user_name']));
            $userId = empty($user)?0:$user->user_id;
            $post   = $post->where('user_id' , $userId);
        }

        if (!empty($params['user_id'])) {
            $post = $post->where('user_id' , $params['user_id']);
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

            $post = $post->where('post_created_at' , '>=' , $start)->where('post_created_at' , '<=' , $end);
        }
        return $post->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate(10);
    }

    public function find($id)
    {
        return $this->model->withTrashed()->find($id);
    }

    public function findByUuid($post_uuid)
    {
        return $this->model->withTrashed()->where('post_uuid' , $post_uuid)->first();
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = 10, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        $postUuid = request()->get('post_uuid' , '');
        $post = $this->model->withTrashed();
        if(!empty($postUuid))
        {
            $post = $post->where('post_uuid' , $postUuid);
        }
        return $post->with(['translations'=>function($query){
            $query->where('post_locale' , locale());
        }])->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
    }

    public function restore($model)
    {
        return $model->restore();
    }

    public function getTopCount()
    {
        return $this->model->where('post_topping' , 1)->count();
    }

    public function findLastTop()
    {
        return $this->model->where('post_topping' , 1)->orderBy('post_topped_at')->first();
    }
    public function findByMany(array $ids)
    {
        $query = $this->model->query();

        if (method_exists($this->model, 'translations')) {
            $query = $query->with(['translations'=>function($query){
                $query->where('post_locale' , locale());
            }]);
        }

        return $query->withTrashed()->whereIn("post_id", $ids)->get();
    }


}
