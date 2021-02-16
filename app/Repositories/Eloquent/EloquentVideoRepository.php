<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\VideoRepository;


class EloquentVideoRepository  extends EloquentBaseRepository implements VideoRepository
{

    public function __construct($model , $request=null)
    {
        parent::__construct($model);
    }

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


    /**
     * @inheritdoc
     */
    public function findByVideoId($videoId)
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->setTableIndex($this->hashDbIndex($videoId))->whereHas('translations', function (Builder $q) use ($videoId) {
                $q->where('video_id', $videoId);
            })->with('translations')->first();
        }
        return $this->model->where('video_id', $videoId)->first();
    }

    public function findByUuid($uuid)
    {
        return $this->model->setTableIndex($uuid)->where('video_uuid', $uuid)->first();
    }

    public function store($data)
    {
        $uuid = Uuid::uuid1();
        $index = $this->hashDbIndex($uuid);
        $index = 1;
        $params['video_uuid'] = $uuid;
        $params['video_cover'] = 1;
        $params['video_url'] = 2;
        $params['video_class_id'] = 3;
        $params['video_producer_id'] = 4;
        $params['en'] = [
            'video_title'=>'dd',
            'video_summary'=>'dd',
            'video_keyword'=>'dd',
        ];
        $params['zh'] = [
            'video_title'=>'dd的',
            'video_summary'=>'dd的',
            'video_keyword'=>'dd的',
        ];
        return $this->model->setTableIndex($index)->create($params);
    }


}
