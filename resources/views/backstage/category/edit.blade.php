@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <style type="text/css">
        table input{ /*可输入区域样式*/
            width:100%;
            height: 25px;
            border:none; /* 输入框不要边框 */
            font-family:Arial;
        }
        .layui-table td, .layui-table th {padding: 5px;}
        .layui-layout-body {max-height: 600px; overflow-y: scroll;}
    </style>
    <form class="layui-form layui-tab-content">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="{{$data->id}}">
        <table class="layui-table">
            <thred>
                <tr>
                    <th>分类名称</th>
                    <th><input type="text" placeholder="仅可输入英文，不可有空格、#" id="name" name="name" value="{{$data->name}}" /></th>
                </tr>
            </thred>
        </table>
        <table id="layui-table" class="layui-table" border="1" align="center">
            <thead>
            <tr>
                <th>序号</th>
                <th>分类语言</th>
                <th>分类名称</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($data->language))
                @foreach($data->language as $key=>$value)
                        @foreach($value as $kk=>$vv)
                            <tr @if($kk==0) id="clo" @endif>
                                <td class="td">{{$key+1}}</td>
                                <td> <input id="language[]" name="language[]" value="{{$kk}}" /></td>
                                <td> <input id="category[]" name="category[]" value="{{$vv}}" /></td>
                            </tr>
                        @endforeach
                @endforeach
            @endif
            </tbody>
            <a href="javascript:;" id="addCol" name="addCol" class="layui-btn layui-btn-xs">增加一行</a>
            <a href="javascript:;" id="delCol" name="delCol" class="layui-btn layui-btn-xs" >删除一行</a>
        </table>
        <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">提交</button>

    </form>

@endsection

@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            formSelects: 'lay/modules/formSelects-v4'
        }).use(['common', 'table', 'layer', 'form', 'element'], function () {
            let table = layui.table,
                form = layui.form,
                common = layui.common,
                $=layui.jquery;
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
                $("table tr:not(:first):not(:first):last").remove();//移除最后一行,并且保留前两行
            }
            //添加一行
            function fun(){
                let $td = $("#clo").clone();       //增加一行,克隆第一个对象
                $(".layui-table").append($td);
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
                console.log(params);
                console.log('ajax start');
                common.ajax("{{url('/backstage/props/category')}}/"+params.id, params , function(res){
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
                console.log('end');
                return false;
            });

        });

    </script>
@endsection
