<?php
return [
    'table' => [
        'header'=>[
            'user_id'=>'评论者',
            'comment_to_id'=>'被评论者',
            'comment_id'=>'ID',
            'comment_content'=>'内容',
            'comment_image'=>'图片',
            'comment_create'=>'创建',
            'comment_update'=>'更新',
            'comment_delete'=>'删除',
            'comment_topping'=>'置顶',
            'comment_op'=>'操作',
        ],
        'button'=>[

        ]
    ],
    'form'=>[
        'select'=>[
            'query'=>[
                'post_uuid'=>'UUID ',
                'comment_key'=>'关键字'
            ]
        ],
        'label'=>[
            'user_name'=>'账号',
            'comment_created_at'=>'日期'
        ],
        'placeholder'=>[
            'comment_name'=>'请输入账号',
            'comment_email'=>'请输入邮箱',
            'comment_ip_address'=>'请输入注册IP',
            'comment_passwd'=>'请输入密码',
            'comment_first_name'=>'请输入姓',
            'comment_last_name'=>'请输入名',
            'comment_language'=>'请输入语言',

            'comment_src'=>'请输入来源',
            'comment_country_id'=>'请选择国家',
            'comment_age'=>'请输入年龄',
            'comment_about'=>'请输入简介',
            'comment_google'=>'请输入google账号',
            'comment_facebook'=>'请输入facebook账号',
            'comment_twitter'=>'请输入twitter账号',
            'comment_instagram'=>'请输入instagram账号',
            'comment_imports'=>'请输入导入数',
            'comment_uploads'=>'请输入上传数',
            'comment_wallet'=>'请输入钱包',
            'comment_balance'=>'请输入消费',
            'comment_video_mon'=>'请输入货币',
            'comment_age_changed'=>'请输入年龄变更',
            'comment_donation_paypal_email'=>'请输入捐赠',
            'comment_upload_limit'=>'请输入上传限制',

        ],
    ],
    'page'=>[
        'edit'=>[
            'bread_crumb'=>[
                'passport'=>'账户管理',
                'comment'=>'账号',
                'edit'=>'编辑'
            ]
        ]
    ],
    'html'=>[
        'comment_list'=>'评论列表'
    ]

];