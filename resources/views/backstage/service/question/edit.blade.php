@extends('layouts.app')
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    <style type="text/css">
        .layui-input { width: 300px;}
        .layui-form-select {z-index: 11000;}
        .layui-input, .layui-form-select {min-width: 300px;}
        .layui-table td, .layui-table th {padding: 5px;}
        .layui-layout-body {max-height: 95%; overflow-y: scroll;}
    </style>
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Title EN：</label>
                    <div class="layui-input-block">
                        <input type="hidden" id="id" name="id" value="{{$data->id}}">
                        <input type="text" id="title_en" name="title_en" required="required" placeholder="Only English" autocomplete="off" class="layui-input" value="{{$data->title['en']}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Title CN：</label>
                    <div class="layui-input-block">
                        <input type="text" style="min-width: 300px;" id="title_zh-CN" name="title_zh-CN" placeholder="仅可中文"  required="required" autocomplete="off" class="layui-input" value="{{$data->title['zh-CN']}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item" style="margin-bottom: 50px;">
                <div class="layui-inline">
                    <label class="layui-form-label">Sort：</label>
                    <div class="layui-input-block">
                        <input type="text" id="sort" name="sort" required="required" autocomplete="off" class="layui-input" value="{{$data->sort}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Status：</label>
                    <div class="layui-input-block">
                        <select name="status">
                            <option value="0" @if($data->status==0) selected @endif>OFFLINE</option>
                            <option value="1" @if($data->status==1) selected @endif>ONLINE</option>
                        </select>
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
                                    <div id="div{{$language}}"></div>
                                    <textarea id="{{$language}}" name="{{$language}}" style="min-width: 1000px; min-height: 30px; display: none;">{{$data->content[$language]}}</textarea>
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
            let form = layui.form,
                common = layui.common,
                $=layui.jquery;
            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
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
                const en = new E("#diven")
            en.config.uploadImgServer = "https://up-z1.qiniup.com/";
                en.config.uploadImgParams = {
                    token: "{{$qn_token['token']}}"
                };
                en.config.uploadFileName = 'file';
                en.config.uploadImgHooks = {
                    // 上传图片之前
                    before: function(xhr) {

                    },
                    // 图片上传并返回了结果，图片插入已成功
                    success: function(xhr) {
                        console.log('success', xhr)
                    },
                    // 图片上传并返回了结果，但图片插入时出错了
                    fail: function(xhr, en, resData) {
                        console.log('fail', resData)
                    },
                    // 上传图片出错，一般为 http 请求的错误
                    error: function(xhr, en, resData) {
                        console.log('error', xhr, resData)
                    },
                    // 上传图片超时
                    timeout: function(xhr) {
                        console.log('timeout', xhr)
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
                en.create();
                const texten = $("#en")
                en.txt.html(texten.val());
                en.config.onchange = function (html) {
                    texten.val(html)
                }
                texten.val(en.txt.html())

            const cn = new E("#divzh-CN")
            cn.config.uploadImgServer = "https://up-z1.qiniup.com/";
            cn.config.uploadImgParams = {
                token: "{{$qn_token['token']}}"
            };
            cn.config.uploadFileName = 'file';
            cn.config.uploadImgHooks = {
                // 上传图片之前
                before: function(xhr) {
                    console.log('before', xhr)
                },
                // 图片上传并返回了结果，图片插入已成功
                success: function(xhr) {
                    console.log('success', xhr)
                },
                // 图片上传并返回了结果，但图片插入时出错了
                fail: function(xhr, cn, resData) {
                    console.log('fail', resData)
                },
                // 上传图片出错，一般为 http 请求的错误
                error: function(xhr, cn, resData) {
                    console.log('error', xhr, resData)
                },
                // 上传图片超时
                timeout: function(xhr) {
                    console.log('timeout', xhr)
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
            cn.create();
            const textcn = $("#zh-CN")
            cn.txt.html(textcn.val());
            cn.config.onchange = function (html) {
                textcn.val(html)
            }
            textcn.val(cn.txt.html())
        });

    </script>
@endsection
