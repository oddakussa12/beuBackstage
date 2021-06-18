@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'user_id', minWidth:180, hide:'true'}">UserId</th>
                <th lay-data="{field:'user_name', minWidth:180}">ShopName</th>
                <th lay-data="{field:'user_nick_name', minWidth:180}">ShopNickName</th>
                <th lay-data="{field:'money', minWidth:180}">Deposits</th>
                <th lay-data="{field:'money_time', minWidth:180}">DepositsTime</th>
                <th lay-data="{field:'balance', minWidth:180}">DepositsBalance</th>

                <th lay-data="{field:'created_at', minWidth:180, hide:'true'}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:180, hide:'true'}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:200, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shops as $shop)
                <tr>
                    <td>{{$shop->user_id}}</td>
                    <td>{{$shop->user_name}}</td>
                    <td>{{$shop->user_nick_name}}</td>
                    <td>{{$shop->money}}</td>
                    <td>{{$shop->money_time}}</td>
                    <td>{{$shop->balance}}</td>
                    <td>{{$shop->created_at}}</td>
                    <td>{{$shop->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $shops->links('vendor.pagination.default') }}
        @else
            {{ $shops->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/",
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['table'], function () {
            const table = layui.table;
            table.init('table', {
                page:false
            });
            table.on('tool(table)', function (obj) {
                var data = obj.data;
                if(obj.event=== 'add'){
                    let device = layui.device();
                    console.log(device);
                    let area = device.android===true || device.ios===true ? ['95%','95%'] : ['400px', '600px'];
                    open(area, '/backstage/business/deposits/create/'+data.user_id)
                }
                if(obj.event=== 'detail'){
                    open(['95%','95%'], '/backstage/business/deposits/money/'+data.user_id)
                }
                if(obj.event=== 'order'){
                    open(['95%','95%'], '/backstage/business/deposits/order/detail/?user_id='+data.user_id)
                }
            });
            function open(area, content, types=2) {
                layer.open({
                    type: types,
                    shadeClose: true,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    content: content,
                });
            }
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="add">{{trans('common.form.button.add')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="order">{{trans('business.table.header.order')}}</a>
    </script>
@endsection