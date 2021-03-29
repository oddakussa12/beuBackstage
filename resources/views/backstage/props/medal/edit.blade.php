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
                <div class="layui-inline">
                    <label class="layui-form-label">Name EN：</label>
                    <div class="layui-input-block">
                        <input type="text" id="name_en" name="name_en" required="required" placeholder="Only English" autocomplete="off" class="layui-input" value="{{$data->name['en']}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Name CN：</label>
                    <div class="layui-input-block">
                        <input type="text" id="name_cn" name="name_cn" required="required" placeholder="仅中文" autocomplete="off" class="layui-input" value="{{$data->name['cn']}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Description EN：</label>
                    <div class="layui-input-block">
                        <input type="text" id="desc_en" name="desc_en" required="required" placeholder="Only English" autocomplete="off" class="layui-input" value="{{$data->desc['en']}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Description CN：</label>
                    <div class="layui-input-block">
                        <input type="text" id="desc_cn" name="desc_cn" required="required" placeholder="仅中文" autocomplete="off" class="layui-input" value="{{$data->desc['cn']}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Score：</label>
                    <div class="layui-input-block">
                        <input type="text" id="score" name="score" required="required" autocomplete="off" class="layui-input" value="{{$data->score}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Category：</label>
                    <div class="layui-input-block">
                        <input id="id" name="id" type="hidden" value="{{$data['id']}}" />
                        <select name="category">
                            @foreach($category as $key=>$name)
                                <option value="{{$key}}" @if($data['category']==$key)  selected @endif>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Image：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="image" name="image" value="{{$data->image}}" />
                        <button type="button" id="upload" name="upload" class="layui-btn"><i class="layui-icon"></i>Upload Image</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <img style="width:100px;height: 100px;" src="{{$data->image}}" id="show" name="show" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">Submit</button>
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
                common.ajax("{{url('/backstage/props/medal')}}/"+params.id, params , function(res){
                    parent.location.reload();
                } , 'patch');
                console.log('end');
                return false;
            });
            $('#upload').each(function(){
                let btn = $(this);
                uploads(btn);
            });

            function uploads(btn, type=''){
                upload.render({
                    elem: btn //绑定元素
                    , url: 'https://up-z1.qiniup.com/' //上传接口
                    , method: 'post'
                    , accept: 'file'
                    , exts:'jpg|png|jpeg|gif'
                    , data: {
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
                        $("#image").val(file);
                        $("#show").attr('src', file).show();
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
