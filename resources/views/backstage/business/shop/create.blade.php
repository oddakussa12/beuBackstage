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
        <form class="layui-form">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.table.header.user_country')}}：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_phone_country" name="user_phone_country" class="layui-input" value="+86">

                        {{--                        <select  name="user_phone_country" lay-search>--}}
                        {{--                            @foreach($countries as $country)--}}
                        {{--                                <option value="{{$country['code']}}">{{$country['name']}}</option>--}}
                        {{--                            @endforeach;--}}
                        {{--                        </select>--}}
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Phone：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="registration_type" name="registration_type" class="layui-input" value="shop">
                        <input type="text" id="user_phone" name="user_phone" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Name：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_name" name="user_name" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">NickName：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_nick_name" name="user_nick_name" required="required" autocomplete="off" class="layui-input" value="">
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
                        <input type="text" id="user_contact" name="user_contact" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Address：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_address" name="user_address" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">BusinessHours：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="business_hours" name="business_hours" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">AuditStatus：</label>
                    <div class="layui-input-inline">
                        <select  name="user_verified">
                            <option value="1">Pass</option>
                            <option value="0">Refuse</option>
                            <option value="-1">UnAudited</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Description：</label>
                    <div class="layui-input-inline">
                        <textarea id="user_about" name="user_about"></textarea>
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
                        <img style="width:200px;display:none;" id="show" name="show" />
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
                        <img style="width:200px;display:none;" id="userBg" name="userBg" />
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
            function uploads(btn, type=''){
                upload.render({
                    elem: btn //绑定元素
                    , url: 'https://up-z1.qiniup.com/' //上传接口
                    , method: 'post'
                    , accept: 'file'
                    , exts: 'jpg|png|jpeg|gif'
                    ,choose: function (obj) {
                        let files = obj.pushFile();
                        obj.preview(function (index, file, result) {
                            browserMD5File(file, function (err, md5) {
                                console.log('md5:'+md5); // 97027eb624f85892c69c4bcec8ab0f11
                            });
                        })
                    }, data: {
                        //key: 'aaa.png',  //自定义文件名
                        token: "{{$qn_token['token']}}"
                    }
                    , before: function (obj) {
                        loadBar.start();
                    }
                    , done: function (res, index, upload) {
                        loadBar.finish();
                        console.log(res);
                        let param = {};
                        param.image = res.name;
                        let file = "https://qneventsource.mmantou.cn/"+res.name;
                        if (type!='bg') {
                            $("#user_avatar").val(file);
                            $("#show").attr('src', file).show();
                        } else {
                            $("#user_bg").val(file);
                            $("#userBg").attr('src', file).show();
                        }
                    }
                    , error: function () {
                        //演示失败状态，并实现重传
                        loadBar.error();
                        return layer.msg('error');
                    }
                });
            }
        });
    </script>
@endsection
