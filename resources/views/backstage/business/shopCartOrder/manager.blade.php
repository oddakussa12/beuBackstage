@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-bg-yellow{
            background-color:yellow;
        }
        .layui-bg-pink{
            background-color:pink;
        }
        .layui-bg-green{
            background-color:green;
        }
        .layui-bg-blue{
            background-color:blue;
        }
        .layui-badge-gray{
            background-color:gray;
        }
        textarea.layui-textarea.layui-table-edit {
            min-width: 300px;
            min-height: 200px;
            z-index: 2;
        }
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type={{$type}}&admin_id=0" class="layui-btn @if(isset($user_id)&&$user_id==0) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">All</a>
                        @foreach($admins as $admin)
                            <a href="?type={{$type}}&admin_id={{$admin->admin_id}}" class="layui-btn @if(isset($user_id)&&$user_id==$admin->admin_id) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{$admin->admin_username}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">

                    <div class="layui-btn-group">
                        <a href="?type=0&admin_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">All</a>
                        @foreach($schedule as $key=>$value)
                            @if($key>=5)
                                <a href="?type={{$key}}&admin_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{$value}}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">OrderId</th>
                <th lay-data="{field:'shop_name', minWidth:180}">ShopName</th>
                <th lay-data="{field:'user_name', minWidth:180}">UserName</th>
                <th lay-data="{field:'order_created_at', minWidth:160}">StartTime</th>
                <th lay-data="{field:'order_schedule', minWidth:180}">OrderStatus</th>
                <th lay-data="{field:'order_status', minWidth:180}">OrderProcess</th>
                <th lay-data="{field:'order_price', minWidth:120}">OrderPrice</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">ShopPrice</th>
                <th lay-data="{field:'order_time', minWidth:180}">OrderTimeConsuming</th>
                <th lay-data="{field:'comment', minWidth:160}">Comment</th>
                <th lay-data="{field:'admin_username', minWidth:160}">Operator</th>
                <th lay-data="{field:'updated_at', minWidth:160}">EndTime</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>{{$order->shop->user_nick_name}}</td>
                    <td>{{$order->user_name}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>{{$schedule[$order->schedule]}}</td>
                    <td><span class="layui-btn layui-btn-sm @if($order->status==1) layui-bg-green @elseif($order->status==2) layui-bg-gray @else layui-btn-warm @endif">
                        @if($order->status==1) Completed @elseif($order->status==2) Canceled @else InProcess @endif</span>
                    </td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->admin_username)){{$order->admin_username}}@endif</td>
                    <td>{{$order->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $orders->links('vendor.pagination.default') }}
        @else
            {{ $orders->appends($appends)->links('vendor.pagination.default') }}
        @endif
        <table class="layui-table" id="table2">
            <tr style="background-color: #f2f2f2;">
                <th lay-data="{field:'order_price', width:180}">All the money received</th>
                <th lay-data="{field:'shop_price', width:180}">Money for the store</th>
                <th lay-data="{field:'shop_price', width:180}">Gross profit</th>
            <tr>
                <td>@if(!empty($money['order_price'])){{$money['order_price']}}@else 0 @endif</td>
                <td>@if(!empty($money['shop_price'])){{$money['shop_price']}}@else 0 @endif</td>
                <td>{{$money['order_price']-$money['shop_price']}}</td>
            </tr>
        </table>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).use(['table'], function () {
            const table = layui.table;
            table.init('table', {
                page:false
            });
            table.on('tool(table)', function (obj) {
                var data = obj.data;
                if (obj.event === 'goods') {
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%', '80%'],
                        offset: 'auto',
                        content: '/backstage/business/order/'+data.id,
                    });
                }
            });
            setTimeout(function() {
                location.reload();
            }, 300000);
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="goods">Goods</a>
    </script>
@endsection