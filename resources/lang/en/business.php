<?php
return [
    'table' => [
        'header'=>[
            'goods'=>[
                'id'=>'GoodsId',
                'name'=>'GoodsName',
                'category'=>'Category',
                'image'=>'GoodsImage',
                'like'=>'GoodsLike',
                'view_num'=>'PageViews',
                'number'=>'GoodsNumber',
                'point'=>'GoodsPoint',
                'price'=>'GoodsPrice',
                'recommendation'=>'Recommendation',
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
            'delivery_order'=>[
                'menu'=>'Menu',
                'goods_name'=>'GoodsName',
                'shop_price'=>'ShopBrokerage',
                'comment'=>'Mark',
            ],
            'order'=>[
                'order_id'=>'OrderId',
                'schedule'=>'OrderSchedule',
                'user_name'=>'UserName',
                'user_contact'=>'UserContact',
                'user_address'=>'UserAddress',
                'status'=>'OrderStatus',
                'order_price'=>'OrderPrice',
                'promo_price'=>'PromoPrice',
                'total_price'=>'TotalPrice(D̶e̶l̶i̶v̶e̶r̶y̶C̶o̶a̶s̶t̶)',
                'brokerage'=>'Brokerage',
                'currency'=>'Currency',
                'promo_code'=>'PromoCode',
                'free_delivery'=>'FreeDelivery',
                'delivery_coast'=>'DeliveryCoast',
                'discount_type'=>'DiscountType',
                'reduction'=>'Reduction',
                'discount'=>'Discount',
                'goods'=>'Goods',
                'discounted_price'=>'DiscountedPrice',
                'profit'=>'Profit',
                'brokerage_percentage'=>'BrokeragePercentage%',
                'shop_price'=>'ShopPrice',
                'comment'=>'Mark',
                'order_time_consuming'=>'TimeConsuming',
                'delivered_at'=>'DeliveredAt',
            ],
            'shop'=>[
                'user_id'=>'ShopId',
                'user_name'=>'ShopName',
                'user_nick_name'=>'ShopNickName',
                'user_address'=>'ShopAddress',
                'user_contact'=>'ShopContact',
                'user_status'=>'Status',
                'user_verified'=>'Reviewed',
                'user_online'=>'Online',
                'user_delivery'=>'Delivery',
                'user_tag'=>'Tag'
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
            'shop_tag'=>[
                'id'=>'ID',
                'tag'=>'Tag',
                'image'=>'Image',
                'status'=>'Status',
            ],
            'goods_comment'=>[
                'id'=>"ID",
                'level'=>"Level",
                'status'=>"Status",
                'verified'=>"Verified",
                'point'=>"Point",
                'quality'=>"Quality",
                'service'=>"Service",
                'comment_user'=>"CommentUser",
                'media'=>"Media",
                'content'=>"Content",
                'to_user'=>"ToUser",
                'child_comment'=>"childComment",
                'verified_at'=>"VerifiedAt",
            ],
            'comment_manager'=>[
                'today_review'=>'TodayReview',
                'month_review'=>'MonthReview',
                'review'=>'TotalReview',
                'today_pass'=>'TodayPass',
                'month_pass'=>'MonthPass',
                'pass'=>'TotalPass',
                'refuse'=>'Refuse',
                'month_refuse'=>'MonthRefuse',
                'type'=>'Type',
                'status'=>'Status',
                'content'=>'Content',
                'created_at'=>'Time',
            ],
            'special_goods'=>[
                'special_price'=>'SpecialPrice',
                'free_delivery'=>'FreeDelivery',
                'packaging_cost'=>'PackagingCost',
                'deadline'=>'Deadline',
                'status'=>'Status',
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
            'view_num'=>'PageViews',
            'order_num'=>'OrderNum',
            'goods_num'=>'GoodsNum',
            'view_history'=>'ViewHistory',
            'manager'=>'Manager',
            'recommend'=>'Recommend',
            'take_out'=>'Delivery',
            'address'=>'Address',
            'service'=>'Service',
            'quality'=>'Quality',
            'media'=>'Media',
            'verified'=>'Reviewed',
            'verified_at'=>'VerifiedAt',
            'content'=>'Content',
            'shop_score'=>'Point',
            'level'=>'Level',
            'child_comment'=>'ChildComment',
            'comment_user'=>'CommentUser',
            'to_user'=>'ToUser',
            'top_user'=>'TopUser',
            'new_order'=>'NewOrder',
            'recommended_at'=>'RecommendedAt',
        ],
        'button'=>[
            'shop_order'=>[
                'goods'=>'Goods'
            ],
            'goods'=>[
                'view_history'=>'ViewHistory',
                'comment'=>'Comment',
                 'special'=>'Special',
            ]
        ]
    ],
    'form'=>[
        'label'=>[
            'tag'=>'Tag',
            'goods'=>[
                'id'=>'GoodsId',
                'name'=>'GoodsName',
                'category'=>'Category',
                'recommendation'=>'Recommendation',
            ],
            'goods_category'=>[
                'is_default'=>'Default',
            ],
            'shop'=>[
                'name'=>'ShopName',
                'user_id'=>'UserId',
                'user_name'=>'UserName',
                'user_nick_name'=>'UserNickName',
                'user_address'=>'UserAddress',
                'user_contact'=>'UserContact',
                'user_phone'=>'UserPhone',
                'user_country'=>'UserCountry',
                'user_verified'=>'UserVerified',
                'user_delivery'=>'UserDelivery',
                'user_online'=>'Online'
            ],
            'shopping_cart'=>[
                'shop_name'=>'ShopName',
                'goods_name'=>'goodsName',
                'user_name'=>'userName'
            ],
            'shop_tag'=>[
                'tag'=>'Tag',
                'tag_content'=>'TagContent',
            ],
            'goods_comment'=>[
                'goods_id'=>"GoodsId",
                'order_by'=>"OrderBy",
                'level'=>"Level",
                'verified'=>"Verified",
            ],
            'comment_manager'=>[
                'status'=>'Status',
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
            'shop_order'=>[
                'shop'=>'Shop',
                'status'=>'Status',
                'schedule'=>'Schedule',
            ],
            'complex'=>[
                'shop_order'=>'Order',
                'delivery_order'=>'Order(old)',
                'shopping_cart'=>'ShoppingCart'
            ],
            'special_goods'=>[
                'special_price'=>'SpecialPrice',
                'free_delivery'=>'FreeDelivery',
                'packaging_cost'=>'PackagingCost',
                'deadline'=>'Deadline',
            ]
        ],
        'placeholder'=>[
            'goods'=>[
                'id'=>'Goods Id',
                'name'=>'Goods Name',
            ],
            'shop'=>[
                'name'=>'Shop Name',
            ],
            'shop_tag'=>[
                'tag'=>'Tag',
                'tag_content'=>'Tag Content',
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
            'special_goods'=>[
                'special_price'=>'SpecialPrice',
                'free_delivery'=>'FreeDelivery',
                'packaging_cost'=>'PackagingCost',
                'deadline'=>'Deadline',
            ]
        ],
        'select'=>[
            'goods_sort'=>[
                'created_at'=>'CreatedAt',
                'like'=>'Like',
            ],
            'shopping_cart'=>[
                'created_at'=>'CreatedAt',
                'number'=>'Number',
            ],
            'shop_review'=>[
                '1'=>'Reviewed',
                '0'=>'rejected',
                '-1'=>'Pending review',
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
                    'desc'=>'DESC',
                    'asc'=>'ASC'
                ],
                'level'=>[
                    ''=>"ALL",
                    '0'=>"NO",
                    '1'=>"YES",
                ],
                'verified'=>[
                    ''=>"ALL",
                    '0'=>"Refuse",
                    '1'=>"Passed",
                    '-1'=>"PendingReview",
                ]
            ],
            'comment_manager'=>[
                'status'=>[
                    ''=>"All",
                    'refuse'=>"Refuse",
                    'pass'=>"Pass",
                ]
            ],
            'promo_code'=>[
                'reduction'=>"Reduction",
                'discount'=>"Discount"
            ]
        ],
    ]

];