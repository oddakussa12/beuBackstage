@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', minWidth:130 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'device_id', minWidth:80}">DeviceId</th>
            <th  lay-data="{field:'user_op', width:100 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->user_id}}</td>
                <td>{{$l->device_id}}</td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="block">Block</a>
        </div>
    </script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'table', 'element'], function () {
            let common = layui.common,
                table = layui.table;
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'block'){ //设备列表
                    const url = "{{url('/backstage/passport/user/device/block')}}";
                    common.confirm("{{trans('common.confirm.unpopular')}}" , function(){
                        common.ajax(url , data , function(res){
                            let msg = res.code===1 ? 'Operation failed, please try again' : "{{trans('common.ajax.result.prompt.update')}}";
                            common.prompt(msg , 1 , 300 , 6 , 't');
                        } , 'post' , function (event,xhr,options,exc) {});
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        })
    </script>
@endsection
