@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    <div class="layui-fluid">
        <form class="layui-form">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.category')}}：</label>
                    <div class="layui-input-inline">
                        <select  name="category">
                            @foreach($categories as $category)
                                <option value="{{$category->name}}">{{$category->name}}</option>
                            @endforeach;
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Camera：</label>
                    <div class="layui-input-inline">
                        <select  name="camera">
                            <option value="front">front</option>
                            <option value="back">back</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.recommend')}}：</label>
                    <div class="layui-input-inline">
                        <select  name="recommendation" >
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.status')}}：</label>
                    <div class="layui-input-inline">
                        <select  name="is_delete" >
                            <option value="1">ONLINE</option>
                            <option value="0">OFFLINE</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.name')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="hash" name="hash" value="">
                        <input type="text" id="name" name="name" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.hot')}}：</label>
                    <div class="layui-input-inline">
                        <select  name="hot" >
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.image')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="cover" name="cover" />
                        <button type="button" id="upload" name="upload" class="layui-btn"><i class="layui-icon"></i>Upload Image</button>
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
                    <label class="layui-form-label">Bundle：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="url" name="url" />
                        <button type="button" id="uploads" name="uploads" class="layui-btn"><i class="layui-icon"></i>Upload Bundle</button>
                        <span style="" id="bundle" name="bundle"></span>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="prop_form" id="btn">Submit</button>
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
                    if(v==''||v==undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/props/props')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
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
                upload.render({
                    elem: btn //绑定元素
                    , url: 'https://up-z1.qiniup.com/' //上传接口
                    , method: 'post'
                    , accept: 'file'
                    , exts: type!=='' ? type : 'jpg|png|jpeg|gif'
                    ,choose: function (obj) {
                        let files = obj.pushFile();
                        obj.preview(function (index, file, result) {
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
                        loadBar.finish();
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
                        loadBar.error();
                        return layer.msg('error');
                    }
                });
            }
        });

    </script>
@endsection
