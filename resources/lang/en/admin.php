<?php
return [
    'table' => [
        'header'=>[
            'admin_id'=>'ORDER',
            'admin_auth'=>'PERMISSION',
            'admin_username'=>'ACCOUNT',
            'admin_email'=>'EMAIL',
            'admin_sex'=>'SEX',
            'admin_status'=>'STATUS',
            'admin_role'=>'ROLE',
            'admin_name'=>'REAL_NAME',
            'admin_country'=>'COUNTRY',
            'admin_created_at'=>'UPDATED_AT',
            'admin_op'=>'OPERATE',
        ]
    ],
    'form'=>[
        'label'=>[
            'admin_username'=>'NICK_NAME',
            'admin_realname'=>'REAL_NAME',
            'admin_email'=>'EMAIL',
            'admin_country'=>'COUNTRY',
            'admin_sex'=>'SEX',
            'admin_status'=>'STATUS',
            'admin_role'=>'ROLE',
            'old_password'=>'Old Password',
            'new_password'=>'New Password',
            'confirm_password'=>'Confirm Password',
            'admin_permission'=>'PERMISSION',
        ],
        'placeholder'=>[
            'admin_username'=>'please input Username',
            'admin_email'=>'please input your email',
            'admin_realname'=>'please enter your real name',
            'admin_country'=>'Please select a country',
            'admin_sex'=>'please enter gender',
            'admin_status'=>'please enter the status'
        ],
    ]

];