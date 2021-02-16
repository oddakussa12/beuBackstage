<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/6/1
 * Time: 15:28
 */


return [
    'form'=>[
        'form_menu_name' => 'MENU',
        'form_menu_auth' => 'AUTH',
        'form_menu_url' => 'URL',
        'form_menu_f_id' => 'SUPERIOR',
        'button'=>[
            'add'=>'add',
            'update'=>'update',
            'reset'=>'reset',
        ]
    ],
    'placeholder' => [
        'menu_name'=>'please enter the menu',
        'menu_auth'=>'please enter permission',
        'menu_url'=>'please enter URl',
    ],
    'table'=>[
        'header'=>[
            'menu_id'=>'ID',
            'menu_p_id'=>'SUPER',
            'menu_name'=>'MENU',
            'menu_auth'=>'PERMISSION',
            'menu_url'=>'URL',
            'menu_op'=>'OPERATE',
        ],
        'button'=>[
            'edit'=>'edit',
            'delete'=>'delete',
        ]
    ],
    'prompt'=>[
        'menu_level_little'=>'The upper menu must be greater than the current menu level.',
        'delete'=>'Please delete the submenu first.',
        'menu_name_required'=>"Menu name cannot be empty.",
        'menu_id_required'=>"Please select the menu to be modified.",
    ],

];
