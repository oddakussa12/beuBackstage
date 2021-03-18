@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    <style type="text/css">
        table input{ /*可输入区域样式*/
            width:100%;
            height: 25px;
            border:none; /* 输入框不要边框 */
            font-family:Arial;
        }
        .layui-form-select {z-index: 100;}
        .layui-table td, .layui-table th {padding: 5px;}
        .layui-layout-body {max-height: 600px; overflow-y: scroll;}
    </style>
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <label class="layui-form-label">Category：</label>
                <div class="layui-inline">
                    <select  name="category">
                        @foreach($categories as $category)
                            <option value="{{$category->name}}" @if($data->category==$category->name) selected @endif>{{$category->name}}</option>
                        @endforeach;
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Title：</label>
                    <div class="layui-input-block">
                        <input type="text" style="min-width: 300px;" id="title" name="title" required="required" autocomplete="off" class="layui-input" value="{{$data->title}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item" style="margin-bottom: 100px;">
                <div class="layui-inline">
                    <label class="layui-form-label">Status：</label>
                    <div class="layui-input-block">
                        <select name="status">
                            <option value="0" @if($data->status==0) selected @endif>DOWN</option>
                            <option value="1" @if($data->status==1) selected @endif>UP</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Sort：</label>
                    <div class="layui-input-block">
                        <input type="text" style="min-width: 300px;" id="sort" name="sort" required="required" autocomplete="off" class="layui-input" value="{{$data->sort}}">
                    </div>
                </div>
            </div>
            <div class="layui-tab" lay-filter="translation">
                <ul class="layui-tab-title">
                    @foreach($languages as $key=>$language)
                        <li @if($key==0) class="layui-this" @endif lay-id="{{$key}}">{{$language}}</li>
                    @endforeach
                </ul>
                <div class="layui-tab-content">
                    @foreach($languages as $key=>$language)
                        <div class="layui-tab-item @if($key==0) layui-show @endif">
                            <div class="layui-form-item layui-form-text">
                                <div class="layui-input-block">
                                    <div id="div{{$language}}">
                                        {{$data->content[$language]}}111111
                                    </div>
                                    <textarea id="{{$language}}" name="{{$language}}" style="min-width: 1000px; min-height: 300px; display: none;">{{$data->content[$language]}}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">Submit</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js"></script>

@endsection

@section('footerScripts')
    @parent
    <script type="text/javascript">
        layui.config({
            base: "{{url('plugin/layui')}}/",
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            formSelects: 'lay/modules/formSelects-v4'
        }).use(['common', 'table', 'layer', 'form', 'upload', 'element'], function () {
            let table = layui.table,
                form = layui.form,
                common = layui.common,
                $=layui.jquery,
                upload = layui.upload;
            //前面的序号1,2,3......
            let i = 1;
            $(".td").each(function(){
                $(this).html(i++);
            });
            $("#addCol").on('click', function () {
                fun();
            });
            $("#delCol").on('click', function () {
                del();
            });
            //删除一行
            function del(){
                $("#layui-table tr:not(:first):not(:first):last").remove();//移除最后一行,并且保留前两行
            }
            //添加一行
            function fun(){
                let $td = $("#clo").clone();       //增加一行,克隆第一个对象
                $("#layui-table").append($td);
                let i = 1;
                $(".td").each(function(){       //增加一行后重新更新序号1,2,3......
                    $(this).html(i++);
                })
                $("table tr:last").find(":input").val('');   //将尾行元素克隆来的保存的值清空
            }
            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/lovbee/question')}}/"+params.id, params, function(res){
                    console.log(res);
                    console.log(res.code);
                    if (res.code!== undefined) {
                        layer.open({
                            title: 'Result'
                            ,content: res.result
                        });
                    }
                    parent.location.reload();
                } , 'patch');
                return false;
            });

            const E = window.wangEditor
            const editor = new E("#diven")
            const $text1 = $('#en')
            editor.config.onchange = function (html) {
                $text1.val(html)
                console.log(111)
                alert($('#diven').html());
                alert($('#en').html());
            }
            editor.config.customUploadImg = function (resultFiles, insertImgFn) {
                console.log(resultFiles);
                console.log(insertImgFn);

                // resultFiles 是 input 中选中的文件列表
                // insertImgFn 是获取图片 url 后，插入到编辑器的方法

                // 上传图片，返回结果，将图片插入到编辑器中
                // insertImgFn(imgUrl)
            }
            editor.create()
            $text1.val(editor.txt.html())

            const editor2 = new E("#divzh-CN")
            const $text2 = $('#zh-CN')
            editor.config.onchange = function (html) {
                $text2.val(html)
            }
            editor2.config.customUploadImg = function (resultFiles, insertImgFn) {
                // resultFiles 是 input 中选中的文件列表
                // insertImgFn 是获取图片 url 后，插入到编辑器的方法

                // 上传图片，返回结果，将图片插入到编辑器中
                // insertImgFn(imgUrl)
            }
            editor2.create()
            $text2.val(editor2.txt.html())
        });

        function upload(btn) {
            let file='';
            upload.render({
                elem: btn //绑定元素
                , url: 'https://up-z1.qiniup.com/' //上传接口
                , method: 'post'
                , accept: 'file'
                , exts: 'jpg|png|jpeg|gif'
                , data: {
                    token: "{{$qn_token['token']}}"
                },choose: function (obj) {
                    let files = obj.pushFile();
                    obj.preview(function (index, file, result) {

                    })
                }
                , done: function (res, index, upload) {
                    console.log(res);
                    let param = {};
                    param.image = res.name;
                    file = "https://qneventsource.mmantou.cn/"+res.name;
                }
                , error: function () {
                    //演示失败状态，并实现重传
                    loadBar.error();
                    return layer.msg('error');
                }
            });
            console.log(file);
            return file;
        }

    </script>
@endsection
