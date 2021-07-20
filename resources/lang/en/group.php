<?php
return [
    'table' => [
        'header'=>[
            'group_id'=>'GroupId',
            'avatar'=>'GroupAvatar',
            'name'=>'GroupName',
            'is_deleted'=>'Disband',
            'member'=>'NumberOfGroupMembers',
            'administrator'=>'Administrator',
            'user_id'=>'Founder',
            'type'=>'Type',
        ],
    ],
    'form'=>[
        'label'=>[
            'administrator'=>'Founder',
            'name'=>'GroupName',
            'group_id'=>'GroupId',
        ],
        'select'=>[
            'sort'=>[
                'created_at'=>'CreatedAt',
                'member'=>'NumberOfGroupMembers',
            ]
        ]
    ]

];