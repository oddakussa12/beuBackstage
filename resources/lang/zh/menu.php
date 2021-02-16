<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/6/1
 * Time: 15:28
 */


return [
    'form'=>[
        'form_menu_name' => '菜单',
        'form_menu_auth' => '权限',
        'form_menu_url' => 'URL',
        'form_menu_f_id' => '上级菜单',
        'button'=>[
            'add'=>'添加',
            'update'=>'修改',
            'reset'=>'重置',
        ]
    ],
    'placeholder' => [
        'menu_name'=>'请输入菜单',
        'menu_auth'=>'请输入权限',
        'menu_url'=>'请输入URl',
    ],
    'table'=>[
        'header'=>[
            'menu_id'=>'ID',
            'menu_p_id'=>'上级菜单',
            'menu_name'=>'菜单',
            'menu_auth'=>'权限',
            'menu_url'=>'URL',
            'menu_op'=>'操作',
        ],
        'button'=>[
            'edit'=>'编辑',
            'delete'=>'删除',
        ]
    ],
    'prompt'=>[
        'menu_level_little'=>'上级菜单必须大于当前菜单等级',
        'delete'=>'请先删除子菜单',
        'menu_name_required'=>"菜单名字不能为空",
        'menu_id_required'=>"请选择要修改的菜单",
    ],

];
