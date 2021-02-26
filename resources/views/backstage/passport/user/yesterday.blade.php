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
                    ,{field:'date', title:'Date', width:120}
                    ,{field:'value', title:'Value'}
                ]]
                ,page: false
                ,response: {
                    statusCode: 200 //重新规定成功的状态码为 200，table 组件默认为 0
                }
                ,parseData: function(res){ //将原始数据解析成 table 组件所规定的数据
                    console.log(res);
                    var item = [];
                    item.push({
                        'title':"DAU",
                        'date':res.dau.date,
                        'value':res.dau.dau.dau,
                    });
                    item.push({
                        'title':"TIMELINE",
                        'date':res.keep.date,
                        'value':res.keep.date,
                    });
                    item.push({
                        'title':"ONE",
                        'date':res.keep.one.one,
                        'value':res.keep.one.oneKeep.one/res.keep.one.oneKeep.new,
                    });
                    item.push({
                        'title':"TWO",
                        'date':res.keep.two.two,
                        'value':res.keep.two.twoKeep.two/res.keep.two.twoKeep.new,
                    });
                    item.push({
                        'title':"THREE",
                        'date':res.keep.three.three,
                        'value':res.keep.three.threeKeep.three/res.keep.three.threeKeep.new,
                    });
                    item.push({
                        'title':"SEVEN",
                        'date':res.keep.seven.seven,
                        'value':res.keep.seven.sevenKeep.seven/res.keep.seven.sevenKeep.new,
                    });
                    item.push({
                        'title':"FOURTEEN",
                        'date':res.keep.fourteen.fourteen,
                        'value':res.keep.fourteen.fourteenKeep.fourteen/res.keep.fourteen.fourteenKeep.new,
                    });
                    item.push({
                        'title':"THIRTY",
                        'date':res.keep.thirty.thirty,
                        'value':res.keep.thirty.thirtyKeep.thirty/res.keep.thirty.thirtyKeep.new,
                    });
                    console.log(item);
                    return {
                        // "code": res.status, //解析接口状态
                        // "msg": res.message, //解析提示文本
                        // "count": res.total, //解析数据长度
                        "data": item
                    };
                }
            });
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
