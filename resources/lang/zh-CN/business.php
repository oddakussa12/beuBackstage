<?php
return [
    'table' => [
        'header'=>[
            'goods'=>[
                'id'=>'商品编号',
                'name'=>'商品名称',
                'category'=>'商品类别',
                'image'=>'图片',
                'like'=>'点赞',
                'view_num'=>'浏览量',
                'number'=>'数量',
                'point'=>'评分',
                'price'=>'价格',
                'recommendation'=>'推荐',
                'status'=>'是否上架',
                'comment'=>'Mark',
            ],
            'shop_order'=>[
                'All'=>'全部',
                'Ordered'=>'已下单',
                'ConfirmOrder'=>'已确认订单',
                'CallDriver'=>'已呼叫骑手',
                'ContactedShop'=>'已联系商家',
                'Delivered'=>'已送达',
                'NoResponse'=>'未联系到',
                'JunkOrder'=>'垃圾订单',
                'UserCancelOrder'=>'用户取消订单',
                'ShopCancelOrder'=>'商家取消订单',
                'Other'=>'其他',
                'InProcess'=>'进行中',
                'Completed'=>'已完成',
                'Canceled'=>'已取消',
            ],
            'discovery_order'=>[
                'menu'=>'菜单',
                'goods_name'=>'商品名',
                'shop_price'=>'商家佣金',
                'comment'=>'Mark',
            ],
            'order'=>[
                'order_id'=>'订单号',
                'schedule'=>'订单进程',
                'user_name'=>'用户名',
                'user_contact'=>'用户联系方式',
                'user_address'=>'用户地址',
                'status'=>'订单状态',
                'order_price'=>'订单金额',
                'brokerage'=>'佣金',
                'currency'=>'订单货币',
                'promo_code'=>'优惠码',
                'free_delivery'=>'免配送费',
                'delivery_coast'=>'配送费',
                'discount_type'=>'优惠类型',
                'reduction'=>'抵扣',
                'discount'=>'折扣',
                'discounted_price'=>'优惠后金额',
                'profit'=>'利润',
                'brokerage_percentage'=>'佣金%',
                'shop_price'=>'商家金额',
                'comment'=>'Mark',
                'order_time_consuming'=>'持续时间',
            ],
            'shop'=>[
                'user_id'=>'商家编号',
                'user_name'=>'商家名称',
                'user_nick_name'=>'商家昵称',
                'user_address'=>'商家地址',
                'user_contact'=>'商家联系方式',
                'user_status'=>'状态',
                'user_verified'=>'审核',
                'user_online'=>'线上',
                'user_delivery'=>'外卖'
            ],
            'promo_code'=>[
                'description'=>'描述',
                'promo_code'=>'优惠码',
                'free_delivery'=>'运费',
                'deadline'=>'截止日期',
                'discount_type'=>'类型',
                'reduction'=>'抵扣',
                'limit'=>'限制',
                'percentage'=>'折扣',
            ],
            'shopping_cart'=>[
                'user_nick_name'=>'用户昵称',
                'shop_user_name'=>'商家名',
                'shop_nick_name'=>'商家昵称',
                'goods_name'=>'商品名',
                'goods_image'=>'商品图片',
                'goods_number'=>'商品数量',
            ],
            'shop_tag'=>[
                'id'=>'ID',
                'tag'=>'Tag',
            ],
            'goods_comment'=>[
                'id'=>"ID",
                'level'=>"优质评论",
                'status'=>"审核状态",
                'verified'=>"审核",
                'point'=>"评分",
                'quality'=>"质量",
                'service'=>"服务",
                'comment_user'=>"评论人",
                'media'=>"视频/图",
                'content'=>"内容",
                'to_user'=>"被评论人",
                'child_comment'=>"回复数",
                'verified_at'=>"审核时间",
            ],
            'comment_manager'=>[
                'today_review'=>'今日审核',
                'month_review'=>'当前月审核',
                'review'=>'总审核审核',
                'today_pass'=>'今日通过',
                'month_pass'=>'当前月通过',
                'pass'=>'总审核通过',
                'refuse'=>'驳回',
                'month_refuse'=>'当前月驳回',
                'type'=>'类型',
                'status'=>'状态',
                'content'=>'内容',
                'created_at'=>'时间',
            ],
            'goods_id'=>'商品编号',
            'goods_name'=>'商品名称',
            'shop_id'=>'店铺编号',
            'shop_name'=>'店铺名称',
            'shop_nick_name'=>'店铺昵称',
            'like'=>'点赞',
            'vip'=>'Vip',
            'price'=>'价格',
            'in-stock'=>'上架',
            'view_num'=>'浏览量',
            'order_num'=>'订单量',
            'goods_num'=>'产品数量',
            'view_history'=>'浏览历史',
            'manager'=>'后台管理者',
            'recommend'=>'是否推荐',
            'take_out'=>'外卖',
            'address'=>'地址',
            'service'=>'服务级别',
            'quality'=>'质量级别',
            'media'=>'图/视频',
            'verified'=>'审核状态',
            'verified_at'=>'审核时间',
            'content'=>'内容',
            'shop_score'=>'评分',
            'level'=>'评论级别',
            'child_comment'=>'二级评论数',
            'comment_user'=>'评论人',
            'to_user'=>'被评论人',
            'top_user'=>'顶级用户',
            'new_order'=>'新订单',
            'recommended_at'=>'推荐时间',
        ],
        'button'=>[
            'shop_order'=>[
                'goods'=>'商品'
            ],
            'goods'=>[
                'view_history'=>'浏览历史',
                'comment'=>'评论'
            ]
        ]
    ],
    'form'=>[
        'label'=>[
            'tag'=>'标签',
            'goods'=>[
                'id'=>'商品ID(编码)',
                'name'=>'商品名',
                'category'=>'商品分类',
                'recommendation'=>'推荐',
            ],
            'goods_category'=>[
                'is_default'=>'默认',
            ],
            'shop'=>[
                'name'=>'商家名',
                'user_id'=>'编号',
                'user_name'=>'商家名',
                'user_nick_name'=>'昵称',
                'user_address'=>'地址',
                'user_contact'=>'联系方式',
                'user_phone'=>'手机号',
                'user_country'=>'商家国家',
                'user_verified'=>'审核',
                'user_delivery'=>'外卖',
                'user_online'=>'线上'
            ],
            'shopping_cart'=>[
                'shop_name'=>'商家名',
                'goods_name'=>'商品名',
                'user_name'=>'用户名'
            ],
            'shop_tag'=>[
                'tag'=>'Tag',
                'tag_content'=>'Tag名',
            ],
            'goods_comment'=>[
                'goods_id'=>"商品ID",
                'order_by'=>"排序",
                'level'=>"优质评论",
                'verified'=>"审核",
            ],
            'comment_manager'=>[
                'status'=>'状态',
            ],
            'promo_code'=>[
                'description'=>'描述',
                'promo_code'=>'优惠码',
                'free_delivery'=>'运费',
                'deadline'=>'截止日期',
                'discount_type'=>'类型',
                'reduction'=>'抵扣',
                'limit'=>'限制',
                'percentage'=>'折扣',
            ],
            'shop_order'=>[
                'shop'=>'商家',
                'status'=>'状态',
                'schedule'=>'进程',
            ],
            'complex'=>[
                'shop_order'=>'订单',
                'delivery_order'=>'订单(老)',
                'shopping_cart'=>'购物车'
            ]
        ],
        'placeholder'=>[
            'goods'=>[
                'id'=>'商品ID(编码)',
                'name'=>'商品名',
            ],
            'shop'=>[
                'name'=>'商家名',
            ],
            'shop_tag'=>[
                'tag'=>'Tag',
                'tag_content'=>'Tag名',
            ],
            'promo_code'=>[
                'description'=>'描述',
                'promo_code'=>'优惠码',
                'free_delivery'=>'运费',
                'deadline'=>'截止日期',
                'discount_type'=>'类型',
                'reduction'=>'抵扣',
                'limit'=>'限制',
                'percentage'=>'折扣',
            ],
        ],
        'select'=>[
            'goods_sort'=>[
                'created_at'=>'时间',
                'like'=>'点赞',
            ],
            'shopping_cart'=>[
                'created_at'=>'时间',
                'number'=>'数量',
            ],
            'shop_review'=>[
                '1'=>'已审核',
                '0'=>'已拒绝',
                '-1'=>'待审核',
            ],
            'user_delivery'=>[
                ''=>'',
                '1'=>'YES',
                '0'=>'NO'
            ],
            'user_online'=>[
                ''=>'',
                '1'=>'YES',
                '0'=>'NO'
            ],
            'goods_comment'=>[
                "order_by"=>[
                    'desc'=>'倒序',
                    'asc'=>'正序'
                ],
                'level'=>[
                    ''=>"全部",
                    '0'=>"NO",
                    '1'=>"YES",
                ],
                'verified'=>[
                    ''=>"全部",
                    '0'=>"驳回",
                    '1'=>"通过",
                    '-1'=>"待审核",
                ]
            ],
            'comment_manager'=>[
                'status'=>[
                    ''=>"全部",
                    'refuse'=>"驳回",
                    'pass'=>"通过",
                ]
            ],
            'promo_code'=>[
                'reduction'=>"抵扣",
                'discount'=>"折扣"
            ]
        ],
    ]

];