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
                            <option value="{{$category->name}}">{{$category->name}}</option>
                        @endforeach;
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Title：</label>
                    <div class="layui-input-block">
                        <input type="text" style="min-width: 300px;" id="title" name="title" required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item" style="margin-bottom: 100px;">
                <div class="layui-inline">
                    <label class="layui-form-label">Status：</label>
                    <div class="layui-input-block">
                        <select  name="status">
                            <option value="0">DOWN</option>
                            <option value="1">UP</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Sort：</label>
                    <div class="layui-input-block">
                        <input type="text" style="min-width: 300px;" id="sort" name="sort" required="required" autocomplete="off" class="layui-input" value="0">
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
                                    </div>
                                    <textarea id="{{$language}}" name="{{$language}}" style="min-width: 1000px; min-height: 300px; display: none;"></textarea>
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
                common.ajax("{{url('/backstage/lovbee/question')}}/", params, function(res){
                    console.log(res);
                    console.log(res.code);
                    if (res.code!== undefined) {
                        layer.open({
                            title: 'Result'
                            ,content: res.result
                        });
                    }
                    parent.location.reload();
                } , 'post');
                return false;
            });

            const E = window.wangEditor
            const editor = new E("#diven")
            editor.config.uploadImgServer = "https://up-z1.qiniup.com/";
            editor.config.uploadImgParams = {
                token: "{{$qn_token['token']}}"
            };
            editor.config.uploadFileName = 'file';
            editor.config.uploadImgHooks = {
                // 上传图片之前
                before: function(xhr) {

                },
                // 图片上传并返回了结果，图片插入已成功
                success: function(xhr) {
                    console.log('success', xhr)
                },
                // 图片上传并返回了结果，但图片插入时出错了
                fail: function(xhr, editor, resData) {
                    console.log('fail', resData)
                },
                // 上传图片出错，一般为 http 请求的错误
                error: function(xhr, editor, resData) {
                    console.log('error', xhr, resData)
                },
                // 上传图片超时
                timeout: function(xhr) {
                    console.log('timeout')
                },
                // 图片上传并返回了结果，想要自己把图片插入到编辑器中
                // 例如服务器端返回的不是 { errno: 0, data: [...] } 这种格式，可使用 customInsert
                customInsert: function(insertImgFn, result) {
                    // result 即服务端返回的接口
                    console.log('customInsert', result)

                    // insertImgFn 可把图片插入到编辑器，传入图片 src ，执行函数即可
                    insertImgFn(result.url+result.name);
                }
            }

            editor.create();
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