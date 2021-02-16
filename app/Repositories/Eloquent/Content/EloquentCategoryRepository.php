<?php

/**
 * @Author: Dell
 * @Date:   2019-08-01 15:43:36
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-05 16:04:37
 */
namespace App\Repositories\Eloquent\Content;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\Content\CategoryRepository;


class EloquentCategoryRepository  extends EloquentBaseRepository implements CategoryRepository
{

    public function getAllCategoryByLocale($locale)
    {
        return $this->model->whereHas('translations' , function ($query) use($locale){
            $query->where('category_locale', $locale);
        })->where('category_isdel',0)->get();
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        if (method_exists($this->model, 'translations')) {
            return $this->model->where(['category_isdel'=>0])->with('translations')->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
        }
        return $this->model->where(['category_isdel'=>0])->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
    }
}