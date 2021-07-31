@extends('layouts.dashboard')

@section('layui-content')
    @php
        $supportedLocales = LaravelLocalization::getSupportedLocales();
    @endphp
    <div style="padding: 20px;">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-tab" lay-filter="modules">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="general">{{trans('config.tab.title.general.head')}}</li>
                        <li lay-id="site">{{trans('config.tab.title.website.head')}}</li>
                        <li lay-id="email">{{trans('config.tab.title.email.head')}}</li>
                        <li lay-id="social">{{trans('config.tab.title.social.head')}}</li>
{{--                        <li lay-id="qiniu_ftp">{{trans('config.tab.title.qiniu_ftp.head')}}</li>--}}
{{--                        <li lay-id="paid">{{trans('config.tab.title.paid.head')}}</li>--}}
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div style="padding: 20px; background-color: #F2F2F2;">
                                <div class="layui-row layui-col-space15">
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.main.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"   lay-filter="config_form">--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.upload_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.upload_video')==='on') checked @endif name="set[general][main][upload_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"/>--}}
{{--                                                        </div>--}}

{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.import_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.import_video')==='on') checked @endif name="set[general][main][import_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.auto_play')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.auto_play')==='on') checked @endif name="set[general][main][auto_play]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.embed_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.embed_video')==='on') checked @endif name="set[general][main][embed_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.history_system')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.history_system')==='on') checked @endif name="set[general][main][history_system]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.article_system')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.article_system')==='on') checked @endif name="set[general][main][article_system]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.down_system')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.down_system')==='on') checked @endif name="set[general][main][down_system]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.import_facebook_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.import_facebook_video')==='on') checked @endif name="set[general][main][import_facebook_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.import_twitch_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.import_twitch_video')==='on') checked @endif name="set[general][main][import_twitch_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.donate_to_channel')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.donate_to_channel')==='on') checked @endif name="set[general][main][donate_to_channel]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.approve_video_before_publishing')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.approve_video_before_publishing')==='on') checked @endif name="set[general][main][approve_video_before_publishing]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.two_factor_setting')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.two_factor_setting')==='on') checked @endif name="set[general][main][two_factor_setting]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.popular_channel')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.popular_channel')==='on') checked @endif name="set[general][main][popular_channel]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.geo_blocking')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.geo_blocking')==='on') checked @endif name="set[general][main][geo_blocking]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.all_create_article')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.all_create_article')==='on') checked @endif name="set[general][main][all_create_article]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.main.item.movie_video')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.main.movie_video')==='on') checked @endif name="set[general][main][movie_video]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.upload.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.upload.item.upload_system_type.name')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="radio" name="set[general][upload][upload_system_type]" value="all" title="{{trans('config.tab.title.general.block.upload.item.upload_system_type.select.all')}}"   @if(config('set.general.upload.upload_system_type')==='all') checked @endif>--}}
{{--                                                            <input type="radio" name="set[general][upload][upload_system_type]" value="pro" title="{{trans('config.tab.title.general.block.upload.item.upload_system_type.select.pro')}}"  @if(config('set.general.upload.upload_system_type')==='pro') checked @endif>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.upload.item.free_user_upload_limit')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][upload][free_user_upload_limit]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.general.upload.free_user_upload_limit')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.upload.item.pro_user_upload_limit')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][upload][pro_user_upload_limit]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.general.upload.pro_user_upload_limit')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="layui-col-md6  layui-clear">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.user.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.user.item.register')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.user.register')==='on') checked @endif name="set[general][user][register]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.user.item.login')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.user.login')==='on') checked @endif name="set[general][user][login]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-inline">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.user.item.delete_user')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="checkbox" @if(config('set.general.user.delete_user')==='on') checked @endif name="set[general][user][delete_user]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.comment.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.comment.item.comment_system.name')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="radio" name="set[general][comment][comment_system]" value="both" title="{{trans('config.tab.title.general.block.comment.item.comment_system.select.both')}}" @if(config('set.general.comment.comment_system')==='both') checked @endif>--}}
{{--                                                            <input type="radio" name="set[general][comment][comment_system]" value="default" title="{{trans('config.tab.title.general.block.comment.item.comment_system.select.default')}}"  @if(config('set.general.comment.comment_system')==='default') checked @endif>--}}
{{--                                                            <input type="radio" name="set[general][comment][comment_system]" value="facebook" title="{{trans('config.tab.title.general.block.comment.item.comment_system.select.facebook')}}"  @if(config('set.general.comment.comment_system')==='facebook') checked @endif>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.comment.item.default_show_comment')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][comment][default_show_comment]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.general.comment.default_show_comment')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.api.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.api.item.youtube_api.name')}}</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][api][youtube_api]" autocomplete="off" placeholder="{{trans('config.tab.title.general.block.api.item.youtube_api.placeholder')}}" class="layui-input" value="{{config('set.general.api.youtube_api')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.api.item.dailymotion_verification_id.name')}}</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][api][dailymotion_id]" autocomplete="off" placeholder="{{trans('config.tab.title.general.block.api.item.dailymotion_verification_id.placeholder')}}" class="layui-input" value="{{config('set.general.api.dailymotion_id')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.api.item.twitch_client_id.name')}}</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][api][twitch_id]" autocomplete="off" placeholder="{{trans('config.tab.title.general.block.api.item.twitch_client_id.placeholder')}}" class="layui-input" value="{{config('set.general.api.twitch_id')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.video.name')}}</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.video.item.video_pagination_limit')}}：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="set[general][video][video_pagination_limit]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.general.video.video_pagination_limit')}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.post.name')}}</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="post_rate" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.post.item.post_rate')}}：</label>
                                                        <div style="color: red;">系数越大,时间权重越大,贴子越旧,得分越低</div>
                                                        <div class="layui-input-block">
                                                            <input type="text" name="set[post][rate]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.post.rate')}}">
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">{{trans('config.tab.title.general.block.post.name')}}</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="post_gravity" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">{{trans('config.tab.title.general.block.post.item.post_rate')}}：</label>
                                                        <div style="color: red;">新版 系数越大,时间权重越大,贴子越旧,得分越低</div>
                                                        <div class="layui-input-block">
                                                            <input type="text" name="set[post][gravity]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.post.gravity')}}">
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">点赞配置</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="fake_like_coefficient" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">点赞权重：</label>
                                                        <div style="color: red;">权重越高，每次点赞增幅越高</div>
                                                        <div class="layui-input-block">
                                                            <input type="text" name="set[fake][like][coefficient]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.fake.like.coefficient')}}">
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">新版开关</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="index_switch" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">SWITCH：</label>
                                                        <div class="layui-input-block">
                                                            <input type="radio" name="set[index][switch]" value="on" title="{{trans('common.cast.switch.on')}}" @if(config('set.index.switch')=='on') checked @endif>
                                                            <input type="radio" name="set[index][switch]" value="off" title="{{trans('common.cast.switch.off')}}" @if(config('set.index.switch')=='off') checked @endif>
                                                        </div>
                                                    </div>

                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">疫情配置</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <input type="hidden" name="config_type" value="dx" />--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">帖子ID：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="text" name="post_uuid" autocomplete="off" class="layui-input"  value="{{$dx['post_uuid']}}">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">安卓：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="radio" name="android" value="on" title="{{trans('common.cast.switch.on')}}" @if($dx['android']>0) checked @endif>--}}
{{--                                                            <input type="radio" name="android" value="off" title="{{trans('common.cast.switch.off')}}" @if($dx['android']<=0) checked @endif>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <label class="layui-form-label">IOS：</label>--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <input type="radio" name="ios" value="on" title="{{trans('common.cast.switch.on')}}" @if($dx['ios']>0) checked @endif>--}}
{{--                                                            <input type="radio" name="ios" value="off" title="{{trans('common.cast.switch.off')}}" @if($dx['ios']<=0) checked @endif>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="layui-col-md6">--}}
{{--                                        <div class="layui-card">--}}
{{--                                            <div class="layui-card-header">热门搜索</div>--}}
{{--                                            <div class="layui-card-body">--}}
{{--                                                <form class="layui-form"  lay-filter="config_form">--}}
{{--                                                    <input type="hidden" name="config_type" value="hot_search" />--}}
{{--                                                    <div class="layui-form-item layui-form-text">--}}

{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <textarea name="hot_search" placeholder="请输入内容" class="layui-textarea"></textarea>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

{{--                                                    <div class="layui-form-item">--}}
{{--                                                        <div class="layui-input-block">--}}
{{--                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">KOL系数配置</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="user_kol_x" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">KOL贴子系数：</label>
                                                        <div class="layui-input-block">
                                                            <input type="text" name="set[user][kol][x]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.user.kol.x')}}">
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">贴子预热</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <input type="hidden" name="config_type" value="post_init_comment_num" />
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">评论数：</label>
                                                        <div class="layui-input-block">
                                                            <input type="text" name="set[post][init][comment][num]" autocomplete="off" class="layui-input" lay-verify="number" value="{{config('set.post.init.comment.num')}}">
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-tab-item layui-show">
                                <div style="padding: 20px; background-color: #F2F2F2;">
                                    <div class="layui-row layui-col-space15">
                                        <div class="layui-col-md6">
                                            <div class="layui-card">
                                                <div class="layui-card-header">{{trans('config.tab.title.website.block.website.name')}}</div>
                                                <div class="layui-card-body">
                                                    <form class="layui-form"  lay-filter="config_form">
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.website.item.site_title')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][website][site_title]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.site_title')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.website.item.site_name')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][website][site_name]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.site_name')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.website.item.site_keyword')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][website][site_keyword]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.site_keyword')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.website.item.site_email')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][website][site_email]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.site_email')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.website.item.site_description')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][website][site_description]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.site_description')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <div class="layui-input-block">
                                                                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-md6">
                                            <div class="layui-card">
                                                <div class="layui-card-header">{{trans('config.tab.title.website.block.other.name')}}</div>
                                                <div class="layui-card-body">
                                                    <form class="layui-form"  lay-filter="config_form">
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.max_upload_size')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][other][max_upload_size]" autocomplete="off" class="layui-input"  value="{{config('set.website.other.max_upload_size')}}" lay-verify="number">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.default_language')}}：</label>
                                                            <div class="layui-input-block">
                                                                <select name="translatable[fallback_locale]" lay-verify="required">
                                                                    @foreach($supportedLocales as $localeCode => $properties)
                                                                        <option value="{{$localeCode}}" @if(config('translatable.locale')&&config('translatable.locale')==$localeCode) selected @elseif(config('translatable.fallback_locale')&&config('translatable.fallback_locale')==$localeCode) selected @else @endif>{!! $properties['native'] !!}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.seo_link')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="radio" name="set[website][other][seo_link]" value="on" title="{{trans('common.cast.switch.on')}}" @if(config('set.website.other.seo_link')==='on') checked @endif>
                                                                <input type="radio" name="set[website][other][seo_link]" value="off" title="{{trans('common.cast.switch.off')}}" @if(config('set.website.other.seo_link')==='off') checked @endif>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.recaptcha')}}：</label>

                                                            <div class="layui-input-block">
                                                                <input type="radio" name="set[website][other][recaptcha]" value="on" title="{{trans('common.cast.switch.on')}}" @if(config('set.website.other.recaptcha')==='on') checked @endif>
                                                                <input type="radio" name="set[website][other][recaptcha]" value="off" title="{{trans('common.cast.switch.off')}}" @if(config('set.website.other.recaptcha')==='off') checked @endif>
                                                            </div>

                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.recaptcha_id')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][other][recaptcha_id]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.recaptcha_id')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.website.block.other.item.google_analytics_id')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[website][other][google_analytics_id]" autocomplete="off" class="layui-input"  value="{{config('set.website.website.google_analytics_id')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <div class="layui-input-block">
                                                                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-tab-item layui-show">
                                <div style="padding: 20px; background-color: #F2F2F2;">
                                    <div class="layui-row layui-col-space15">
                                        <div class="layui-col-md6">
                                            <div class="layui-card">
                                                <div class="layui-card-header">{{trans('config.tab.title.email.block.email.name')}}</div>
                                                <div class="layui-card-body">
                                                    <form class="layui-form"  lay-filter="config_form">
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.server_type')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="radio" name="set[email][server_type]" value="smtp" title="SMTP Server" @if(config('set.email.server_type')==='smtp') checked @endif>
                                                                <input type="radio" name="set[email][server_type]" value="server" title="Server Mail(Default)" @if(config('set.email.server_type')==='server') checked @endif>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.smtp_host')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[email][smtp_host]" autocomplete="off" class="layui-input"  value="{{config('set.email.smtp_host')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.smtp_username')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[email][smtp_username]" autocomplete="off" class="layui-input"  value="{{config('set.email.smtp_username')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.smtp_password')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="password" name="set[email][smtp_password]" autocomplete="off" class="layui-input"  value="{{config('set.email.smtp_password')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.smtp_port')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[email][smtp_port]" autocomplete="off" class="layui-input"  value="{{config('set.email.smtp_port')}}" lay-verify="number">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.email.block.email.item.smtp_encryption')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="radio" name="set[email][smtp_encryption]" value="tls" title="TLS" @if(config('set.email.smtp_encryption')==='tls') checked @endif>
                                                                <input type="radio" name="set[email][smtp_encryption]" value="ssl" title="SSL" @if(config('set.email.smtp_encryption')==='ssl') checked @endif>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <div class="layui-input-block">
                                                                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-tab-item layui-show">
                                <div style="padding: 20px; background-color: #F2F2F2;">
                                    <div class="layui-row layui-col-space15">
                                        <div class="layui-col-md6">
                                            <div class="layui-card">
                                                <div class="layui-card-header">{{trans('config.tab.title.social.block.switch.name')}}</div>
                                                <div class="layui-card-body">
                                                    <form class="layui-form"  lay-filter="config_form">
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.switch.item.facebook')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="checkbox" @if(config('set.social_login.facebook.switch')==='on') checked @endif name="set[social_login][facebook][switch]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"/>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.switch.item.twitter')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="checkbox" @if(config('set.social_login.twitter.switch')==='on') checked @endif name="set[social_login][twitter][switch]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"/>
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.switch.item.google')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="checkbox" @if(config('set.social_login.google.switch')==='on') checked @endif name="set[social_login][google][switch]" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"/>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-md6">
                                            <div class="layui-card">
                                                <div class="layui-card-header">{{trans('config.tab.title.social.block.api.name')}}</div>
                                                <div class="layui-card-body">
                                                    <form class="layui-form"  lay-filter="config_form">
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.facebook_api_id')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][facebook][facebook_api_id]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.facebook.facebook_api_id')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.facebook_api_key')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][facebook][facebook_api_key]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.facebook.facebook_api_key')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.twitter_api_id')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][twitter][twitter_api_id]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.twitter.twitter_api_id')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.twitter_api_key')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][twitter][twitter_api_key]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.twitter.twitter_api_key')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.google_api_id')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][google][google_api_id]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.google.google_api_id')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <label class="layui-form-label">{{trans('config.tab.title.social.block.api.item.google_api_key')}}：</label>
                                                            <div class="layui-input-block">
                                                                <input type="text" name="set[social_login][google][google_api_key]" autocomplete="off" class="layui-input"  value="{{config('set.social_login.google.google_api_key')}}">
                                                            </div>
                                                        </div>
                                                        <div class="layui-form-item">
                                                            <div class="layui-input-block">
                                                                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['form', 'layedit', 'laydate' , 'element' , 'common'], function() {
            var $ = layui.jquery
                , element = layui.element,
                form = layui.form
                ,layer = layui.layer
                ,layedit = layui.layedit
                ,common = layui.common
                ,laydate = layui.laydate;
            //Hash地址的定位
            var layid = location.hash.replace(/^#modules=/, '');
            element.on('tab(modules)', function (elem) {
                location.hash = 'modules=' + $(this).attr('lay-id');
            });
            form.on('submit(config_form)', function(data){
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });
            form.on('submit(config_submit_btn)', function(data){
                @if(!Auth::user()->can('config.store'))
                data.form.reset();
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.elem);
                form.render();
                return false;
                @endif
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/config/')}}" , data.field , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        } , 'post');
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });

            //监听指定开关
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                data.elem.checked = !checked;
                @if(!Auth::user()->can('config.store'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                var name = $(data.elem).attr('name');
                if(checked)
                {
                    var params = '{"'+name+'":"on"}';
                }else{
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{LaravelLocalization::localizeUrl('/backstage/config/')}}" , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'post' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});

            });
        });
    </script>
@endsection