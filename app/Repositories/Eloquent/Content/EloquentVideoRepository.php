<?php

/**
 * @Author: Dell
 * @Date:   2019-07-22 17:21:58
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-07 18:17:00
 */
namespace App\Repositories\Eloquent\Content;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\Content\VideoRepository;


class EloquentVideoRepository  extends EloquentBaseRepository implements VideoRepository
{
	public function test(){
		return 'test';
	}
	public function videowithsubtitlepaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        if (method_exists($this->model, 'translations')) {
            return $this->model
            	->with('translations')
				->with(['subtitle'=>function($query){
					$query->where(['video_locale'=>locale()])
						->select('video_subtitle_id','video_id','video_locale','video_subtitle_url')
						->get();
					}])
				->orderBy($this->model->getCreatedAtColumn(), 'DESC')
				->paginate($perPage , $columns , $pageName , $page);
        }
        return $this->model->with('subtitle')->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
	}
	public function find($id)
	{
		if (method_exists($this->model, 'translations'))
		{
			return $this->model->with('translations')->with('subtitle')->find($id);
		}
		return $this->model->with('subtitle')->find($id);
	}

}