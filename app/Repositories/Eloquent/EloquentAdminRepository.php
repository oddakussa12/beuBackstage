<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\AdminRepository;


class EloquentAdminRepository  extends EloquentBaseRepository implements AdminRepository
{
    //$perPage = null, $columns = ['*'], $pageName = 'page', $page = null
    public function getDefaultPasswordField()
    {
        return $this->model->default_password_field;
    }
}
