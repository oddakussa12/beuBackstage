@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    <style>
        textarea {width: 35.22vw; border: #e6e6e6 1px solid; height: 100px;}
        @media screen and (max-width:768px) {
            textarea {width: 100%; border: #e6e6e6 1px solid; height: 100px;}
        }
    </style>
    <div class="layui-fluid">
        <br>
        <form class="layui-form" method="post">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.table.header.user_country')}}：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_phone_country" name="user_phone_country" class="layui-input" value="{{$phone->user_phone_country}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Phone：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_phone" name="user_phone" required="required" autocomplete="off" class="layui-input" value="{{$phone->user_phone}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Name：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_name" name="user_name" required="required" autocomplete="off" class="layui-input" value="{{$user->user_name}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">NickName：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_nick_name" name="user_nick_name" required="required" autocomplete="off" class="layui-input" value="{{$user->user_nick_name}}">
                    </div>
                </div>
                <div class="layui-inline" style="display: none">
                    <label class="layui-form-label">Password：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="password" name="password" required="required" autocomplete="off" class="layui-input" value="owsasdl234">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Contact：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_contact" name="user_contact" required="required" autocomplete="off" class="layui-input" value="{{$user->user_contact}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Address：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_address" name="user_address" required="required" autocomplete="off" class="layui-input" value="{{$user->user_address}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">BusinessHours：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="business_hours" name="business_hours" required="required" autocomplete="off" class="layui-input" value="{{$user->business_hours}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">AuditStatus：</label>
                    <div class="layui-input-inline">
                        <select  name="user_verified">
                            <option value="1"  @if($user->user_verified==1)  selected @endif>Pass</option>
                            <option value="0"  @if($user->user_verified==0)  selected @endif>Refuse</option>
                            <option value="-1" @if($user->user_verified==-1) selected @endif>UnAudited</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Description：</label>
                    <div class="layui-input-inline">
                        <textarea id="user_about" name="user_about">{{$user->user_about}}</textarea>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_avatar')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="user_avatar" name="user_avatar" />
                        <button type="button" id="upload" name="upload" class="layui-btn"><i class="layui-icon"></i>Upload Avatar</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <img style="width:100px;height: 100px;" src="{{$user->user_avatar}}" id="show" name="show" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_avatar')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="user_bg" name="user_bg" />
                        <button type="button" id="uploads" name="uploads" class="layui-btn"><i class="layui-icon"></i>Upload BgImage</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <img style="width:100px;height: 100px;" src="{{$user->user_bg}}" id="userBg" name="userBg" />

                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="prop_form" id="btn">Submit</button>
            </div>
        </form>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common', 'table', 'layer', 'element', 'upload', 'loadBar'], function () {
            var form = layui.form,
                layer = layui.layer,
                loadBar = layui.loadBar,
                common = layui.common,
                $=layui.jquery,
                upload = layui.upload;
            form.on('submit(prop_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/business/shop')}}", params , function(res){
                    if (res.message!==undefined) {
                        common.tips(res.message);
                        layer.closeAll();
                    } else {
                        common.tips('Success');
                        parent.location.reload();
                    }
                } , 'post');
                return false;
            });
            $('#upload').each(function(){
                let btn = $(this);
                uploads(btn);
            });
            $('#uploads').each(function(){
                let btn = $(this);
                uploads(btn, 'bg');
            });
            function uploads(btn, type='') {
                upload.render({
                    elem: btn
                    ,accept: 'images'
                    ,auto: false
                    ,choose:function (obj , test){
                        var formData = new FormData();
                        var files = obj.pushFile();
                        // console.log(files);
                        var keys = Object.keys(files);
                        var end = keys[keys.length-1]
                        var file = files[end];

                        common.ajax("{{config('common.lovbee_domain')}}api/aws/image/form?file="+file.name , {} , function(res){
                            // image.config.url = res.action;
                            for (const p in res.form) {
                                formData.append(p, res.form[p]);
                            }
                            console.log(file);
                            console.log(res);
                            formData.append('file',file);
                            console.log(formData);
                            $.ajax({
                                url:res.action,
                                type:'POST',
                                data: formData,
                                async:false,
                                processData:false,
                                cache:false,
                                contentType:false,
                                xhr:jqureAjaxXhrOnProgress(function(e){
                                    var percent=e.loaded / e.total;
                                    percent = Math.round((percent + Number.EPSILON) * 100);
                                    $("#upload_progress").html(percent);
                                }),
                                beforeSend: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                                    common.prompt('uploaded<span id="upload_progress"></span>%' , 16 , 0 , 0 , 'auto' , undefined , [0.8, '#393D49']);
                                },
                                success:function(data){
                                    console.log(res.domain+res.form.key)
                                    let file = res.domain+res.form.key;
                                    if (type!=='bg') {
                                        $("#user_avatar").val(file);
                                        $("#show").attr('src', file).show();
                                    } else {
                                        $("#user_bg").val(file);
                                        $("#userBg").attr('src', file).show();
                                    }
                                },
                                error:function(){
                                    alert('upload failed');
                                },
                                complete:function (){
                                    layer.closeAll();
                                }
                            })
                        } , 'get' , undefined , undefined , false);
                    }
                });
            }
            var jqureAjaxXhrOnProgress = function(fun) {
                jqureAjaxXhrOnProgress.onprogress = fun; //绑定监听
                //使用闭包实现监听绑
                return function() {
                    //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
                    var xhr = $.ajaxSettings.xhr();
                    //判断监听函数是否为函数
                    if (typeof jqureAjaxXhrOnProgress.onprogress !== 'function')
                        return xhr;
                    //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
                    if (jqureAjaxXhrOnProgress.onprogress && xhr.upload) {
                        xhr.upload.onprogress = jqureAjaxXhrOnProgress.onprogress;
                    }
                    return xhr;
                }
            }
        });
    </script>
@endsection
