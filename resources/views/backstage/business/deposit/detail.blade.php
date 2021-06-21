@extends('layouts.app')
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
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">OrderId</th>
                <th lay-data="{field:'shop_name', minWidth:180}">ShopName</th>
                <th lay-data="{field:'user_name', minWidth:180}">UserName</th>
                <th lay-data="{field:'order_status', minWidth:180}">OrderStatus</th>
                <th lay-data="{field:'order_menu', minWidth:200}">Menu</th>
                <th lay-data="{field:'order_price', minWidth:120}">OrderPrice</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">ShopPrice</th>
                <th lay-data="{field:'order_time', minWidth:180}">OrderTimeConsuming</th>
                <th lay-data="{field:'comment', minWidth:160}">Comment</th>
                <th lay-data="{field:'admin_username', minWidth:160}">Operator</th>
                <th lay-data="{field:'updated_at', minWidth:160}">EndTime</th>
                <th lay-data="{field:'deposit', minWidth:160}">DepositBalance</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>{{$order->shop->user_nick_name}}</td>
                    <td>{{$order->user_name}}</td>
                    <td>{{$status[$order->status]}}</td>
                    <td>@if(!empty($order->menu)){{$order->menu}}@endif</td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->admin_username)){{$order->admin_username}}@endif</td>
                    <td>{{$order->updated_at}}</td>
                    <td>{{$order->deposit}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $orders->links('vendor.pagination.default') }}
        @else
            {{ $orders->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
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
        });
    </script>
@endsection