@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    {{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <label class="layui-form-label">Category：</label>
                <div class="layui-input-block">
                    <input id="id" name="id" type="hidden" value="{{$data['id']}}" />
                    <select  name="category" style="width: 100px;">
                        @foreach($categories as $category)
                            <option value="{{$category->name}}" @if($data['category']==$category->name)  selected @endif>{{$category->name}}</option>
                        @endforeach;
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Camera：</label>
                    <div class="layui-input-block">
                        <select  name="camera">
                            <option value="front" @if($data->default=='front') selected @endif>front</option>
                            <option value="back"  @if($data->default=='back') selected @endif>back</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Recommendation：</label>
                    <div class="layui-input-block">
                        <select  name="recommendation" >
                            <option value="0" @if($data->recommendation==0) selected @endif>NO</option>
                            <option value="1" @if($data->recommendation==1) selected @endif>YES</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Status：</label>
                    <div class="layui-input-block">
                        <select  name="is_delete" >
                            <option value="1" @if($data->is_delete==1) selected @endif>ONLINE</option>
                            <option value="0" @if($data->is_delete==0) selected @endif>OFFLINE</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Name：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="hash" name="hash" value="{{$data->hash}}">
                        <input type="text" id="name" name="name" required="required" autocomplete="off" class="layui-input" value="{{$data->name}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Hotting：</label>
                    <div class="layui-input-block">
                        <select  name="hot" >
                            <option value="0" @if($data->hot==0) selected @endif>否</option>
                            <option value="1" @if($data->hot==1) selected @endif>是</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Image：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="cover" name="cover" value="{{$data->cover}}" />
                        <button type="button" id="upload" name="upload" class="layui-btn"><i class="layui-icon"></i>Upload Image</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <img style="width:100px;height: 100px;" src="{{$data->cover}}" id="show" name="show" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Bundle：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="url" name="url" value="{{$data->url}}" />
                        <button type="button" id="uploads" name="uploads" class="layui-btn"><i class="layui-icon"></i>Upload Bundle</button>
                    </div>
                    <div class="layui-input-block">
                        <span id="bundle"  style="word-break: break-all;"  name="bundle">{{$data->url}}</span>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">Submit</button>
            </div>
        </form>
    </div>
@endsection
<script src="/js/bundle.js"></script>
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
                common.ajax("{{url('/backstage/props/props')}}/"+params.id, params , function(res){
                    parent.location.reload();
                } , 'patch');
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
                            console.log(file);
                            browserMD5File(file, function (err, md5) {
                                if (type!=='') {
                                    $("#hash").val(md5);
                                }
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
@endsection
