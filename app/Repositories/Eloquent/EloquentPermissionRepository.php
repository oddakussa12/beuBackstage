<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\PermissionRepository;


class EloquentPermissionRepository  extends EloquentBaseRepository implements PermissionRepository
{
    //$perPage = null, $columns = ['*'], $pageName = 'page', $page = null
    public function getSortPermissions()
    {
        $permissions = $this->model->all();
        $sortPermissions = array();
        foreach ($permissions as $k=>$permission)
        {
            $permissionName = $permission['name'];
            $index = stripos($permissionName , '.');
            if($index!==false)
            {
                $sort = substr($permissionName , 0 , $index);
                if(!array_key_exists($sort , $sortPermissions))
                {
                    $sortPermissions[$sort] = array();
                }
                array_push($sortPermissions[$sort] , $permission);
            }else{
                unset($permissions[$k]);
            }
        }
        return $sortPermissions;
    }
}
