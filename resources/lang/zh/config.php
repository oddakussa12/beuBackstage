<?php

return [
    'tab'=>[
        'title'=>[
            'general'=>[
              'head'=>'常规配置',
              'block'=>[
                  'user'=>[
                      'name'=>'用户配置',
                      'item'=>[
                          'register'=>'用户注册',
                          'login'=>'用户登录',
                          'delete_user'=>'用户注销',
                      ]
                  ],
                  'main'=>[
                      'name'=>'主要配置',
                      'item'=>[
                          'upload_video'=>'视频上传',
                          'import_video'=>'视频导入',
                          'auto_play'=>'自动播放',
                          'embed_video'=>'视频嵌入',
                          'history_system'=>'历史播放',
                          'article_system'=>'文章系统',
                          'down_system'=>'下载系统',
                          'import_facebook_video'=>'导入facebook视频',
                          'import_twitch_video'=>'导入twitch视频',
                          'donate_to_channel'=>'捐赠给频道',
                          'approve_video_before_publishing'=>'用户发布是否需要审核',
                          'two_factor_setting'=>'二次认证',
                          'popular_channel'=>'热门频道',
                          'geo_blocking'=>'区域播放',
                          'all_create_article'=>'谁可以创建内容',
                          'movie_video'=>'是否开启影视',
                      ]
                  ],
                  'api'=>[
                      'name'=>'API配置',
                      'item'=>[
                          'youtube_api'=>[
                              'name'=>'YouTube API key',
                              'placeholder'=>'请输入YouTube API key'
                          ],
                          'dailymotion_verification_id'=>[
                              'name'=>'Dailymotion verification ID',
                              'placeholder'=>'请输入Dailymotion verification ID'
                          ],
                          'twitch_client_id'=>[
                              'name'=>'Twitch Client Id',
                              'placeholder'=>'请输入Twitch Client Id'
                          ],
                      ]
                  ],
                  'comment'=>[
                      'name'=>'评论配置',
                      'item'=>[
                          'comment_system'=>[
                              'name'=>'评论系统',
                              'select'=>[
                                  'both'=>'全部',
                                  'default'=>'站点默认',
                                  'facebook'=>'facebook',
                              ]
                          ],
                          'default_show_comment'=>'默认显示评论条数',
                      ]
                  ],
                  'upload'=>[
                      'name'=>'上传配置',
                      'item'=>[
                          'upload_system_type'=>[
                              'name'=>'上传类型',
                              'select'=>[
                                  'all'=>'全部',
                                  'pro'=>'会员',
                              ]
                          ],
                          'free_user_upload_limit'=>'普通用户上传限制',
                          'pro_user_upload_limit'=>'会员上传限制',
                      ]
                  ],
                  'video'=>[
                      'name'=>'视频配置',
                      'item'=>[
                          'video_pagination_limit'=>'视频默认显示条数'
                      ]
                  ],
              ],
            ],

            'website'=>[
                'head'=>'站点配置',
                'block'=>[
                    'website'=>[
                        'name'=>'站点配置',
                        'item'=>[
                            'site_title'=>'站点标题',
                            'site_name'=>'站点名称',
                            'site_keyword'=>'站点关键词',
                            'site_email'=>'站点邮箱',
                            'site_description'=>'站点描述',
                        ]
                    ],
                    'other'=>[
                        'name'=>'其他配置',
                        'item'=>[
                            'max_upload_size'=>'最大上传值',
                            'default_language'=>'默认语言',
                            'seo_link'=>'SEO链接',
                            'recaptcha'=>'验证码',
                            'recaptcha_id'=>'GOOGLE RECAPTCHA ID',
                            'google_analytics_id'=>'GOOGLE ANALYTICS ID'
                        ]

                    ]
                ]
            ],
            'email'=>[
                'head'=>'邮箱配置',
                'block'=>[
                    'email'=>[
                        'name'=>'邮箱配置',
                        'item'=>[
                            'server_type'=>'邮箱服务器类型',
                            'smtp_host'=>'SMTP HOST',
                            'smtp_username'=>'SMTP USERNAME',
                            'smtp_password'=>'SMTP PASSWORD',
                            'smtp_port'=>'SMTP PORT',
                            'smtp_encryption'=>'SMTP ENCRYPTION',
                        ]
                    ]
                ]
            ],
            'social'=>[
                'head'=>'社交登录配置',
                'block'=>[
                    'api'=>[
                        'name'=>'API 设置',
                        'item'=>[
                            'facebook_api_id'=>'FACEBOOK API ID',
                            'facebook_api_key'=>'FACEBOOK API KEY',
                            'twitter_api_id'=>'TWITTER API ID',
                            'twitter_api_key'=>'TWITTER API KEY',
                            'google_api_id'=>'GOOGLE API ID',
                            'google_api_key'=>'GOOGLE API KEY',
                        ]
                    ],
                    'switch'=>[
                        'name'=>'社交登录设置',
                        'item'=>[
                            'facebook'=>'facebook',
                            'twitter'=>'twitter',
                            'google'=>'google',
                        ]
                    ]
                ]
            ],
            'qiniu_ftp'=>[
                'head'=>'七牛云配置',
                'block'=>[
                    'qiniu'=>[
                        'name'=>'七牛云配置',
                        'item'=>[
                            'bucket_name'=>'BUCKET NAME',
                            'qiniu_key'=>'QINIU KEY',
                            'qiniu_secret_key'=>'QINIU SECRET KEY',
                        ]
                    ]
                ]
            ],
            'paid'=>[
                'head'=>'支付配置',
                'block'=>[

                ]
            ]
        ]
    ],
    'common'=>[
        'button'=>[
            'save'=>'保存',
            'test_connect'=>'测试连接',
            'submit'=>'提交'
        ]
    ]
];