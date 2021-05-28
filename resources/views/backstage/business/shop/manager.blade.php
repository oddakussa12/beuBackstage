@extends('layouts.dashboard')
@section('layui-content')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'admin_id', minWidth:100, hide:'true'}">ID</th>
                <th lay-data="{field:'admin_username', minWidth:100, fixed:'left'}">昵称</th>
                <th lay-data="{field:'admin_realname', minWidth:100, fixed:'left'}">姓名</th>
                <th lay-data="{field:'admin_country', minWidth:100, fixed:'left'}">国家</th>
                <th lay-data="{field:'todayClaim', minWidth:100}">今日领取</th>
                <th lay-data="{field:'monthClaim', minWidth:100}">本月领取</th>
                <th lay-data="{field:'totalClaim', minWidth:100}">总领取</th>
                <th lay-data="{field:'today', minWidth:100}">今日审核</th>
                <th lay-data="{field:'month', minWidth:100}">本月审核</th>
                <th lay-data="{field:'total', minWidth:100}">总审核</th>
                <th lay-data="{field:'pass', minWidth:100}">通过</th>
                <th lay-data="{field:'passMonth', minWidth:100}">本月通过</th>
                <th lay-data="{field:'refuse', minWidth:100}">拒绝</th>
                <th lay-data="{field:'refuseMonth', minWidth:100}">本月拒绝</th>
                <th lay-data="{field:'recommend', minWidth:100}">通过并推荐</th>
                <th lay-data="{field:'recommendMonth', minWidth:130}">本月通过并推荐</th>
                <th lay-data="{field:'lastTime', minWidth:160}">最后审核时间</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($admins as $value)
                <tr>
                    <td>{{$value->admin_id}}</td>
                    <td>{{$value->admin_username}}</td>
                    <td>{{$value->admin_realname}}</td>
                    <td>{{$value->admin_country}}</td>
                    <td>{{$value->todayClaim}}</td>
                    <td>{{$value->monthClaim}}</td>
                    <td>{{$value->totalClaim}}</td>
                    <td>{{$value->today}}</td>
                    <td>{{$value->month}}</td>
                    <td>{{$value->total}}</td>
                    <td>{{$value->pass}}</td>
                    <td>{{$value->passMonth}}</td>
                    <td>{{$value->refuse}}</td>
                    <td>{{$value->refuseMonth}}</td>
                    <td>{{$value->recommend}}</td>
                    <td>{{$value->recommendMonth}}</td>
                    <td>{{$value->lastTime}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $admins->links('vendor.pagination.default') }}
        @else
            {{ $admins->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['table' , 'layer'], function () {
            const layer = layui.layer,
                table = layui.table;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                layer.open({
                    type: 2,
                    shadeClose: true,
                    shade: 0.8,
                    area: ['95%','95%'],
                    offset: 'auto',
                    scrollbar:true,
                    content: '/backstage/business/shop/manager/detail/'+data.admin_id,
                });
            });
        });

    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
    </script>
@endsection