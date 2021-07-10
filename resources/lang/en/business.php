<?php
return [
    'table' => [
        'header'=>[
            'goods'=>[
                'id'=>'GoodsId',
                'name'=>'GoodsName',
                'category'=>'GoodsCategory',
                'image'=>'GoodsImage',
                'like'=>'GoodsLike',
                'view_num'=>'PageViews',
                'number'=>'GoodsNumber',
                'point'=>'GoodsPoint',
                'price'=>'GoodsPrice',
                'recommendation'=>'GoodsRecommendation',
                'status'=>'In-stock',
                'comment'=>'Mark',
            ],
            'shop_order'=>[
                'All'=>'All',
                'Ordered'=>'Ordered',
                'ConfirmOrder'=>'ConfirmOrder',
                'CallDriver'=>'CallDriver',
                'ContactedShop'=>'ContactedShop',
                'Delivered'=>'Delivered',
                'NoResponse'=>'NoResponse',
                'JunkOrder'=>'JunkOrder',
                'UserCancelOrder'=>'UserCancelOrder',
                'ShopCancelOrder'=>'ShopCancelOrder',
                'Other'=>'Other',
                'InProcess'=>'InProcess',
                'Completed'=>'Completed',
                'Canceled'=>'Canceled',
            ],
            'order'=>[
                'order_id'=>'OrderId',
                'schedule'=>'OrderSchedule',
                'user_name'=>'UserName',
                'user_contact'=>'UserContact',
                'user_address'=>'UserAddress',
                'status'=>'OrderStatus',
                'order_price'=>'OrderPrice',
                'brokerage'=>'Brokerage',
                'currency'=>'Currency',
                'promo_code'=>'PromoCode',
                'free_delivery'=>'FreeDelivery',
                'delivery_coast'=>'DeliveryCoast',
                'discount_type'=>'DiscountType',
                'reduction'=>'Reduction',
                'discount'=>'Discount',
                'discounted_price'=>'DiscountedPrice',
                'profit'=>'Profit',
                'brokerage_percentage'=>'BrokeragePercentage%',
                'shop_price'=>'ShopPrice',
                'comment'=>'Mark',
                'order_time_consuming'=>'TimeConsuming',
            ],
            'shop'=>[
                'user_id'=>'ShopId',
                'user_name'=>'ShopName',
                'user_nick_name'=>'ShopNickName',
                'user_address'=>'ShopAddress',
                'user_contact'=>'ShopContact',
            ],
            'promo_code'=>[
                'description'=>'Description',
                'promo_code'=>'PromoCode',
                'free_delivery'=>'FreeDelivery',
                'deadline'=>'Deadline',
                'discount_type'=>'DiscountType',
                'reduction'=>'Reduction',
                'limit'=>'Limit',
                'percentage'=>'Percentage',
            ],
            'shopping_cart'=>[
                'user_nick_name'=>'UserNickName',
                'shop_user_name'=>'ShopUserName',
                'shop_nick_name'=>'ShopNickName',
                'goods_name'=>'GoodsName',
                'goods_image'=>'GoodsImage',
                'goods_number'=>'GoodsNumber',
            ],
            'goods_id'=>'GoodsId',
            'goods_name'=>'GoodsName',
            'shop_id'=>'ShopId',
            'shop_name'=>'ShopName',
            'shop_nick_name'=>'ShopNickName',
            'like'=>'Like',
            'vip'=>'Vip',
            'price'=>'Price',
            'in-stock'=>'In-stock',
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
                'goods'=>'Goods'
            ]
        ]
    ],
    'form'=>[
        'label'=>[
            'tag'=>'Tag',
            'goods'=>[
                'id'=>'GoodsId',
                'name'=>'GoodsName',
                'category'=>'GoodsCategory',
                'recommendation'=>'GoodsRecommendation',
            ],
            'shop'=>[
                'name'=>'ShopName',
            ],
            'shopping_cart'=>[
                'shop_name'=>'ShopName',
                'goods_name'=>'goodsName',
                'user_name'=>'userName'
            ],
        ],
        'placeholder'=>[
            'goods'=>[
                'id'=>'GoodsId',
                'name'=>'GoodsName',
            ],
            'shop'=>[
                'name'=>'ShopName',
            ]
        ],
        'select'=>[
            'goods_sort'=>[
                'created_at'=>'Time',
                'like'=>'Like',
                'price'=>'Price',
                'view_num'=>'PageViews',
            ],
            'shopping_cart'=>[
                'created_at'=>'Date',
                'number'=>'Number',
            ]
        ],
    ]

];