@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    {{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <style>.layui-fluid textarea { height: 100px; width: 260px;}</style>
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Name CN：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="name_cn" name="name_cn" required="required" placeholder="仅中文" autocomplete="off" class="layui-input">{{$data->name['cn']}} </textarea>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Name EN：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="name_en" name="name_en" required="required" placeholder="Only English" autocomplete="off" class="layui-input">{{$data->name['en']}}</textarea>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Name ID：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="name_id" name="name_id" required="required" placeholder="id" autocomplete="off" class="layui-input">@if(!empty($data->name['id'])){{$data->name['id']}}@endif</textarea>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Description CN：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="desc_cn" name="desc_cn" required="required" placeholder="仅中文" autocomplete="off" class="layui-input">{{$data->desc['cn']}}</textarea>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Description EN：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="desc_en" name="desc_en" required="required" placeholder="Only English" autocomplete="off" class="layui-input">{{$data->desc['en']}}</textarea>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Description ID：</label>
                    <div class="layui-input-block">
                        <textarea type="text" id="desc_id" name="desc_id" required="required" placeholder="id" autocomplete="off" class="layui-input">@if(!empty($data->desc['id'])){{$data->desc['id']}}@endif</textarea>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Score：</label>
                    <div class="layui-input-block">
                        <input type="text" id="score" name="score" disabled required="required" autocomplete="off" class="layui-input layui-disabled" value="{{$data->score}}" />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Category：</label>
                    <div class="layui-input-block">
                        <input id="id" name="id" type="hidden" value="{{$data['id']}}" />
                        <select name="category" disabled>
                            @foreach($category as $key=>$name)
                                <option value="{{$key}}" @if($data['category']==$key)  selected @endif>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Light Image：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="image_light" name="image_light" value="{{$data->image_light}}" />
                        <button type="button" id="uploads" name="uploads" class="layui-btn"><i class="layui-icon"></i>Upload Image</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <img style="width:113px;height: 100px;" src="{{$data->image_light}}" id="show_image_light" name="show_image_light" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Black Image：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="image" name="image" value="{{$data->image}}" />
                        <button type="button" id="upload" name="upload" class="layui-btn"><i class="layui-icon"></i>Upload Image</button>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <img style="width:113px;height: 100px;" src="{{$data->image}}" id="show_image" name="show_image" />
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
            $('#uploads').each(function(){
                let btn = $(this);
                uploads(btn, 'image_light');
            });
            $('#upload').each(function(){
                let btn = $(this);
                uploads(btn, 'image');
            });
            function uploads(btn, divId){
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
                        $("#"+divId).val(file);
                        $("#show_"+divId).attr('src', file).show();
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
