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
                <label class="layui-form-label">请选择国家：</label>
                <div class="layui-input-inline layui-form-select">
                    <input id="id" name="id" type="hidden" value="{{$data['id']}}" />
                    <select  name="country" lay-verify="" lay-search>
                        <option value="">{{trans('user.form.placeholder.user_country_id')}}</option>
                        @foreach($counties  as $country)
                            <option value="{{$country['code']}}" @if($data['country']==$country['code']) selected @endif>{{$country['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="layui-form-label">商品类型：</label>
                <div class="layui-input-inline">
                    <select  name="type" lay-verify="">
                        <option value="1">虚拟</option>
                        <option value="2">实物</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">商品名称：</label>
                    <div class="layui-input-block">
                        <input type="text" id="name" name="name" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">兑换限制：</label>
                    <div class="layui-input-block">
                        <input type="text" id="limiting" name="limiting" required="required" autocomplete="off" class="layui-input" value="0">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">实际价格：</label>
                    <div class="layui-input-block">
                        <input type="text" id="price" name="price" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">消耗积分：</label>
                    <div class="layui-input-block">
                        <input type="text" id="score" name="score" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">开始时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="start_time" name="start_time" placeholder="开始时间" required="required" autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结束时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="end_time" name="end_time" placeholder="结束时间" required="required" autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">库存：</label>
                    <div class="layui-input-block">
                        <input type="text" id="total" name="total" required="required" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">状态：</label>
                    <div class="layui-input-block">
                        <select  name="status" lay-verify="">
                            <option value="0">下架</option>
                            <option value="1">上架</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">图片：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="image" name="image" />
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
                table = layui.table,
                loadBar = layui.loadBar,
                laydate = layui.laydate,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery,
                upload = layui.upload,
                element = layui.element,
            formSelects = layui.formSelects;
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
                common.ajax("{{url('/backstage/invitation/good')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
                console.log('end');
                return false;
            });
            $('#upload').each(function(){
                var btn = $(this);
                upload.render({
                    elem: btn //绑定元素
                    , url: 'https://upload-as0.qiniup.com/' //上传接口
                    , method: 'post'
                    , data: {
                        //key: 'aaa.png',  //自定义文件名
                        token: "{{$qn_token['token']}}"
                    }
                    , before: function (obj) {
                        loadBar.start();
                        //预读本地文件示例，不支持ie8
                        // obj.preview(function (index, file, result) {
                        //     $('#demo1').attr('src', result); //图片链接（base64）
                        // });
                    }
                    , done: function (res, index, upload) {
                        var param = {};
                        param.image = res.name;
                        $("#image").val(res.name);
                        $("#show").attr('src', "{{config('common.qnUploadDomain.thumbnail_domain')}}"+res.name+"?imageMogr2/auto-orient/interlace/1|imageslim").show();
                    }
                    , error: function () {
                        //演示失败状态，并实现重传
                        return layer.msg('error');
                    }

                });
            });
        });
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
