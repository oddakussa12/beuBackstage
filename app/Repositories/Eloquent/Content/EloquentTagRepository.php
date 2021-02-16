<?php

/**
 * @Author: Dell
 * @Date:   2019-08-06 16:42:19
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-06 17:16:12
 */
namespace App\Repositories\Eloquent\Content;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\Content\TagRepository;


class EloquentTagRepository  extends EloquentBaseRepository implements TagRepository
{

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
        }
        return $this->model->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
    }
}