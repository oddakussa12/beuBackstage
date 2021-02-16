@extends('layouts.dashboard')
@section('layui-content')
    @php
        $qn_token = qnToken('qn_avatar_sia');
    @endphp
    <fieldset class="layui-elem-field layui-field-title">
        <legend>
        <span class="layui-breadcrumb">
            <a href="javascript:;">{{trans('user.page.edit.bread_crumb.passport')}}</a>
            <a href="{{url(locale().'/backstage/passport/user')}}">{{trans('user.page.edit.bread_crumb.user')}}</a>
            <a href="">{{trans('user.page.edit.bread_crumb.edit')}}</a>
        </span>
        </legend>
    </fieldset>
    @php
        $common_state_list = config('common.common_state');
        krsort($common_state_list);
    @endphp
        <div class="layui-fluid" id="layui-fluid" style="margin-top: 20px;margin-left: 20px;">
            <form class="layui-form layui-tab-content layui-form-pane"  lay-filter="user_form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_name')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_name" value="{{$user->user_name}}"  placeholder="{{trans('user.form.placeholder.user_name')}}" lay-verify="user_name" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_email')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_email" value="{{$user->user_email}}"  placeholder="{{trans('user.form.placeholder.user_email')}}" lay-verify="user_email" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_ip_address')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_ip_address" value="{{$user->user_ip_address}}"  placeholder="{{trans('user.form.placeholder.user_ip_address')}}" lay-verify="user_ip_address" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>


            {{--        <div class="layui-inline">--}}
            {{--            <label class="layui-form-label">{{trans('user.form.label.user_passwd')}}</label>--}}
            {{--            <div class="layui-input-block">--}}
            {{--                <input type="password" name="user_passwd" value="{{$user->user_passwd}}"  placeholder="{{trans('user.form.placeholder.user_passwd')}}" lay-verify="user_passwd" autocomplete="off" class="layui-input">--}}
            {{--            </div>--}}
            {{--        </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_email_code')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_email_code" value="{{$user->user_email_code}}"   lay-verify="user_email_code" autocomplete="off" class="layui-input" style="width:200%">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_first_name')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_first_name" value="{{$user->user_first_name}}"  placeholder="{{trans('user.form.placeholder.user_first_name')}}" lay-verify="user_first_name" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_last_name')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_last_name" value="{{$user->user_last_name}}" placeholder="{{trans('user.form.placeholder.user_last_name')}}" lay-verify="user_last_name" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_age')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_age" value="{{$user->user_age}}"   lay-verify="user_age" autocomplete="off" class="layui-input">
                        </div>
                    </div>
{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_age_changed')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_age_changed" value="{{$user->user_age_changed}}"   lay-verify="user_age_changed" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_language')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_language" value="{{$user->user_language}}"   lay-verify="user_language" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_country_id')}}</label>
                        <div class="layui-input-block">

                            <select name="user_country_id" >
                                <option value="">-{{trans('common.form.placeholder.select_first')}}-</option>
                                @php
                                    $country_list = config('common.countries_name');
                                @endphp
                                @foreach($country_list as $k=>$country)
                                    <option id="country_class_id_{{$k}}" value="{{$k}}" @if($k==$user->user_country_id) selected @endif >{{$country}}</option>
                                @endforeach
                            </select>

                            {{--                <input type="text" name="user_country_id" value="{{$user->user_country_id}}"   lay-verify="user_country_id" autocomplete="off" class="layui-input">--}}
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_gender')}}</label>
                        <div class="layui-input-block">
                            @php
                                $sex_list = config('common.sex');
                                krsort($sex_list);
                            @endphp
                            @foreach($sex_list as $k=>$sex)
                                <input type="radio" name="user_gender" value="{{$k}}" title="{{trans($sex)}}"  @if($user->user_gender==$k) checked @endif>
                            @endforeach
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_src')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_src" value="{{$user->user_src}}"   lay-verify="user_src" autocomplete="off" class="layui-input  layui-disabled" disabled>
                        </div>
                    </div>


{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_device_id')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_device_id" value="{{$user->user_device_id}}"   lay-verify="user_device_id" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}


                </div>

                <div class="layui-form-item">
                     <div class="layui-inline">
                         <label class="layui-form-label">{{trans('user.form.label.user_avatar')}}</label>
                         <div class="layui-input-block">
                             <input type="text" name="user_avatar" value="{{$user->user_avatar}}"   lay-verify="user_avatar" autocomplete="off" class="layui-input" readonly>
                         </div>
                     </div>
                     <div class="layui-inline">
                         <div class="layui-upload-list" style="margin:0">
                             <img lay-src="{{'//'.$qn_token['domain'].$user->user_avatar.'?imageView2/1/w/40/h/40'}}" class="user_avatar" width="40px">
                         </div>
                     </div>
                     <div class="layui-inline" style="width: auto;">
                         <button type="button" class="layui-btn layui-btn-danger" id="user_avatar"><i class="layui-icon"></i>上传图片</button>
                     </div>
                     <div class="layui-inline">头像的尺寸限定150x150px,大小在50kb以内</div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_cover')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_cover" value="{{$user->user_cover}}"   lay-verify="user_cover" autocomplete="off" class="layui-input" readonly>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-upload-list" style="margin:0">
                            <img lay-src="{{'//'.$qn_token['domain'].$user->user_cover.'?imageView2/1/w/40/h/40'}}" class="user_cover" width="40px">
                        </div>
                    </div>
                    <div class="layui-inline" style="width: auto;">
                        <button type="button" class="layui-btn layui-btn-danger" id="user_cover"><i class="layui-icon"></i>上传图片</button>
                    </div>
                    <div class="layui-inline">头像的尺寸限定150x150px,大小在50kb以内</div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_google')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_google" value="{{$user->user_google}}"   lay-verify="user_google" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_facebook')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_facebook" value="{{$user->user_facebook}}"   lay-verify="user_facebook" autocomplete="off" class="layui-input  layui-disabled" disabled>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_twitter')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_twitter" value="{{$user->user_twitter}}"   lay-verify="user_twitter" autocomplete="off" class="layui-input  layui-disabled" disabled>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_instagram')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_instagram" value="{{$user->user_instagram}}"   lay-verify="user_instagram" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_active')}}</label>
                        <div class="layui-input-block">
                            @php
                                $active_list = config('common.active');
                                krsort($active_list);
                            @endphp
                            @foreach($active_list as $k=>$active)
                                <input type="radio" name="user_active" value="{{$k}}" title="{{trans($active)}}"  @if($user->user_active==$k) checked @endif>
                            @endforeach
                        </div>
                    </div>

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_verified')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            @foreach($common_state_list as $k=>$state)--}}
{{--                                <input type="radio" name="user_verified" value="{{$k}}" title="{{trans($state)}}"  @if($user->user_verified==$k) checked @endif>--}}
{{--                            @endforeach--}}

{{--            --}}{{--                <input type="text" name="user_verified" value="{{$user->user_verified}}"   lay-verify="user_verified" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}



{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_is_pro')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            @foreach($common_state_list as $k=>$state)--}}
{{--                                <input type="radio" name="user_is_pro" value="{{$k}}" title="{{trans($state)}}"  @if($user->user_is_pro==$k) checked @endif>--}}
{{--                            @endforeach--}}


{{--            --}}{{--                <input type="text" name="user_is_pro" value="{{$user->user_is_pro}}"   lay-verify="user_is_pro" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_two_factor')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            @foreach($common_state_list as $k=>$state)--}}
{{--                                <input type="radio" name="user_two_factor" value="{{$k}}" title="{{trans($state)}}"  @if($user->user_two_factor==$k) checked @endif>--}}
{{--                            @endforeach--}}
{{--                            --}}{{--                <input type="text" name="user_two_factor" value="{{$user->user_two_factor}}"   lay-verify="user_two_factor" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_video_mon')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            @foreach($common_state_list as $k=>$state)--}}
{{--                                <input type="radio" name="user_video_mon" value="{{$k}}" title="{{trans($state)}}"  @if($user->user_video_mon==$k) checked @endif>--}}
{{--                            @endforeach--}}
{{--                            --}}{{--                <input type="text" name="user_video_mon" value="{{$user->user_video_mon}}"   lay-verify="user_video_mon" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="layui-form-item">
{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_imports')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_imports" value="{{$user->user_imports}}"   lay-verify="user_imports" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_uploads')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_uploads" value="{{$user->user_uploads}}"   lay-verify="user_uploads" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_upload_limit')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_upload_limit" value="{{$user->user_upload_limit}}"    lay-verify="user_upload_limit" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                </div>

                <div class="layui-form-item">

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_score')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_score" value="{{$user->user_score}}"   lay-verify="user_score" autocomplete="off" class="layui-input">
                        </div>
                    </div>

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_balance')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_balance" value="{{$user->user_balance}}"    lay-verify="user_balance" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_donation_paypal_email')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_donation_paypal_email" value="{{$user->user_donation_paypal_email}}"    lay-verify="user_donation_paypal_email" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="layui-form-item">
{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_active_time')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_active_time" value="{{$user->user_active_time}}"   lay-verify="user_active_time" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_last_active')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_last_active" value="{{$user->user_last_active}}"   lay-verify="user_last_active" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('user.form.label.user_registered')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_created_at" value="{{$user->user_created_at}}"   lay-verify="user_created_at" autocomplete="off" class="layui-input layui-disabled" disabled>
                        </div>
                    </div>
{{--                    <div class="layui-inline">--}}
{{--                        <label class="layui-form-label">{{trans('user.form.label.user_active_expire')}}</label>--}}
{{--                        <div class="layui-input-block">--}}
{{--                            <input type="text" name="user_active_expire" value="{{$user->user_active_expire}}"   lay-verify="user_active_expire" autocomplete="off" class="layui-input">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="layui-form-item layui-form-text" style="width:50%;">
            {{--        <div class="layui-block">--}}
                        <label class="layui-form-label">{{trans('user.form.label.user_about')}}</label>
                        <div class="layui-input-block">
                            <textarea name="user_about" placeholder="{{trans('user.form.placeholder.user_about')}}" class="layui-textarea">{{$user->user_about}}</textarea>
            {{--                <input type="text" name="user_about" value="{{$user->user_about}}"   lay-verify="user_about" autocomplete="off" class="layui-input">--}}
                        </div>
            {{--        </div>--}}
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">

                        <button class="layui-btn" lay-submit lay-filter="form_submit_update">{{trans('common.form.button.update')}}</button>
                        <button type="reset" class="layui-btn layui-btn-primary">{{trans('common.form.button.reset')}}</button>
                    </div>
                </div>
            </form>
        </div>

@endsection
@section('footerScripts')
    @parent
{{--    <script src="{{ asset('plugin/layui/lay/modules/plupload.full.min.js') }}"></script>--}}
    <script src="{{ asset('plugin/layui/lay/modules/qiniu.min.js') }}"></script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'form' , 'flow' , 'upload' , 'loadBar'], function () {
            var $ = layui.jquery,
                form = layui.form,
                flow = layui.flow,
                common = layui.common,
                upload = layui.upload,
                loadBar = layui.loadBar;
                var token = "{{$qn_token['token']}}";
                var domain = "{{$qn_token['domain']}}";
                console.log(domain);
                var browse_button = ["user_avatar","user_cover"];
                $.each(browse_button , function(i , j){
                    var btn = $("#"+j);
                    upload.render({
                        elem: btn //绑定元素
                        , url: 'https://upload-as0.qiniup.com/' //上传接口
                        , method: 'post'
                        , data: {
                            token: token
                        }
                        , before: function (obj) {
                            loadBar.start();
                            //预读本地文件示例，不支持ie8
                            // obj.preview(function (index, file, result) {
                            //     $('#demo1').attr('src', result); //图片链接（base64）
                            // });
                        }
                        , done: function (res, index, upload) {
                            var data = {};
                            var id = "{{$id}}";
                            data[btn.attr('id')] = res.name;
                            common.ajax("{{ url('/backstage/passport/user') }}/"+id, data, function (res) {
                                loadBar.finish();
                                window.location.reload();
                            } , 'put' , function (e,xhr,opt) {
                                var msg = '未知错误';
                                if((opt=='Unprocessable Entity'&&e.status==422)||e.status==423)
                                {
                                    var res = e.responseJSON;
                                    msg = res.errors.email[0];
                                }else if(e.status==302)
                                {
                                    var res = e.responseJSON;
                                    msg = res.message;
                                }
                                common.prompt(msg , 5 , 1000 , 6 , 'auto' ,function () {
                                    loadBar.error();
                                });
                            });
                        }
                        , error: function () {
                            //演示失败状态，并实现重传
                            return layer.msg('error');
                        }

                    });
                });

            form.on('submit(form_submit_update)', function(data){
                common.ajax("{{url('/backstage/passport/user/'.$user->user_id)}}" , data.field , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
            });
            form.on('submit(user_form)', function(){
                return false;
            });
            flow.lazyimg();
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
