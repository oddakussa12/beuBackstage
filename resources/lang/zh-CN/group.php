<?php
return [
    'table' => [
        'header'=>[
            'group_id'=>'群号',
            'avatar'=>'群头像',
            'name'=>'群名',
            'is_deleted'=>'解散',
            'member'=>'群成员',
            'administrator'=>'群管理员',
            'user_id'=>'创始人',
            'type'=>'类型',
        ],
    ],
    'form'=>[
        'label'=>[
            'administrator'=>'群管理员',
            'name'=>'群名',
            'group_id'=>'群号',
        ],
        'select'=>[
            'sort'=>[
                'created_at'=>'日期',
                'member'=>'群成员数',
            ]
        ]
    ]

];