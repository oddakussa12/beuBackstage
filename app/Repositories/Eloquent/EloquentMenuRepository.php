<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\MenuRepository;


class EloquentMenuRepository  extends EloquentBaseRepository implements MenuRepository
{
    
    public function getMenuTree($array , $pid =0, $level = 0)
    {
        $list = [];
        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['menu_p_id'] == $pid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中

                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $tempArr = $this->getMenuTree($array, $value['menu_id'], $level+1);
                if(!empty($tempArr)){
                    $value['child'] = $tempArr;
                }
                $list[] = $value;

            }
        }
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function update($model, $data)
    {
        $model->update($data);

        return $model;
    }






}
