@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <table class="layui-table"  lay-filter="user_table" id="user_table">
        </table>
    </div>
@endsection
@section('footerScripts')
    @parent


    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer,
                flow = layui.flow,
                timePicker = layui.timePicker,
                laydate = layui.laydate;


            table.render({
                elem: '#user_table'
                ,url:'/backstage/passport/user/yesterday'
                ,toolbar: true
                ,totalRow: true
                ,cols: [[
                    ,{field:'title', title:'Title', width:120, fixed: 'left'}
                    ,{field:'value', title:'Value', width:120}
                ]]
                ,page: false
                ,response: {
                    statusCode: 200 //重新规定成功的状态码为 200，table 组件默认为 0
                }
                ,parseData: function(res){ //将原始数据解析成 table 组件所规定的数据
                    console.log(res);
                    // return {
                    //     "code": res.status, //解析接口状态
                    //     "msg": res.message, //解析提示文本
                    //     "count": res.total, //解析数据长度
                    //     "data": res.rows.item //解析数据列表
                    // };
                }
            });
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
