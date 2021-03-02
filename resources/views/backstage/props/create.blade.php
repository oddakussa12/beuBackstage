@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_image_sia');
    @endphp
    {{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <fieldset class="layui-elem-field layui-field-title">
        <legend></legend>
    </fieldset>
    <div class="layui-container">
        <style>
            .layui-layout-body {overflow: auto;}
            .layui-form-label {padding:9px; width: 112px;}
            .layui-input {width: 200px;}
            .layui-input-block {width:200px; padding-left:20px;}
        </style>
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <label class="layui-form-label">选择类别：</label>
                <div class="layui-inline">
                    <input id="id" name="id" type="hidden" value="{{$data['id']}}" />
                    <select  name="category"  lay-search>
                        <option value="test">test</option>
                        <option value="test2">test2</option>
                        <option value="test3">test3</option>
                        <option value="test4">test4</option>
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">摄像头：</label>
                    <div class="layui-input-block">
                        <select  name="camera">
                            <option value="front">front</option>
                            <option value="back">back</option>
                        </select>
                    </div>
                </div>
            </div>



            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">道具名称：</label>
                    <div class="layui-input-block">
                        <input type="text" id="name" name="name" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">是否默认：</label>
                    <div class="layui-input-block">
                        <select  name="default" >
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">是否推荐：</label>
                    <div class="layui-input-block">
                        <select  name="recommendation" >
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">状态：</label>
                    <div class="layui-input-block">
                        <select  name="status" >
                            <option value="0">下架</option>
                            <option value="1">上架</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Hash(MD5)：</label>
                    <div class="layui-input-block">
                        <input type="text" id="hash" name="hash" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">图片：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="cover" name="cover" />
                        <button type="button" id="upload" name="upload">上传图片</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <img style="width:200px;display:none;" id="show" name="show" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">上传Bundle：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="url" name="url" />
                        <button type="button" id="uploads" name="uploads">上传文件</button>
                        <span style="width:200px;" id="bundle" name="bundle"></span>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">提交</button>
            </div>
        </form>
    </div>
@endsection

@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        </div>
    </script>

    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            formSelects: 'lay/modules/formSelects-v4'
        }).use(['common', 'tree', 'table', 'layer', 'carousel', 'element', 'upload', 'loadBar','laydate', 'formSelects'], function () {
            var form = layui.form,
                layer = layui.layer,
                loadBar = layui.loadBar,
                laydate = layui.laydate,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery,
                upload = layui.upload;
            carousel.render({
                elem: '#top_carousel'
                ,width: '1200px'
                ,height: '600px'
                ,interval: 1000
            });
            //formSelects.render('admin_roles');
            //执行一个laydate实例
            lay('.time').each(function(){
                laydate.render({
                    elem: this, //指定元素
                    type: 'datetime',
                    calendar: true
                });
            });

            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined) {return true;}
                    params[k] = v;
                });
                console.log(params);
                console.log('ajax start');
                common.ajax("{{url('/backstage/props/props')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
                console.log('end');
                return false;
            });
            $('#upload').each(function(){
                let btn = $(this);
                uploads(btn);
            });
            $('#uploads').each(function(){
                let btn = $(this);
                uploads(btn, 'bundle');
            });
            function uploads(btn, type=''){
                let fileName = '';
                upload.render({
                    elem: btn //绑定元素
                    //, url: 'https://upload-as0.qiniup.com/' //上传接口
                    , url: 'https://up-z1.qiniup.com/' //上传接口
                    , method: 'post'
                    , accept: 'file'
                    , exts: type!=='' ? type : 'jpg|png|jpeg|gif'
                    ,choose: function (obj) {
                        let files = obj.pushFile();
                        obj.preview(function (index, file, result) {
                            fileName = files[index].name;
                            console.log(fileName);
                        })
                    }, data: {
                        //key: 'aaa.png',  //自定义文件名
                        token: "{{$qn_token['token']}}"
                    }
                    , before: function (obj) {
                        loadBar.start();
                    }
                    , done: function (res, index, upload) {

                        console.log(res);
                        let param = {};
                        param.image = res.name;
                        let file = "https://qneventsource.mmantou.cn/"+res.name;
                        if (type!='bundle') {
                            $("#cover").val(file);
                            $("#show").attr('src', file).show();
                        } else {
                            $("#url").val(file);
                            $("#bundle").text(file);
                        }
                    }
                    , error: function () {
                        //演示失败状态，并实现重传
                        return layer.msg('error');
                    }

                });
            }
        });
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
