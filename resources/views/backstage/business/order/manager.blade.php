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
                        <a href="?type={{$type}}&user_id=0" class="layui-btn @if(isset($user_id)&&$user_id==0) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">All</a>
                        @foreach($admins as $admin)
                            <a href="?type={{$type}}&user_id={{$admin->admin_id}}" class="layui-btn @if(isset($user_id)&&$user_id==$admin->admin_id) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{$admin->admin_username}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type=0&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">All</a>
                        <a href="?type=1&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='1') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Ordered</a>
                        <a href="?type=2&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='2') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ConfirmOrder</a>
                        <a href="?type=3&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='3') layui-btn-disabled @else layui-btn-normal @endif" target="_self">CallDriver</a>
                        <a href="?type=4&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='4') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ContactedShop</a>
                        <a href="?type=5&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='5') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Delivered</a>
                        <a href="?type=6&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='6') layui-btn-disabled @else layui-btn-normal @endif" target="_self">NoResponse</a>
                        <a href="?type=7&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='7') layui-btn-disabled @else layui-btn-normal @endif" target="_self">JunkOrder</a>
                        <a href="?type=8&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='8') layui-btn-disabled @else layui-btn-normal @endif" target="_self">UserCancelOrder</a>
                        <a href="?type=9&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='9') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ShopCancelOrder</a>
                        <a href="?type=10&user_id={{$user_id}}" class="layui-btn @if(isset($type)&&$type=='10') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Other</a>
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
                <th lay-data="{field:'order_status', minWidth:180}">OrderStatus</th>
                <th lay-data="{field:'order_menu', minWidth:200, edit: 'textarea'}">Menu</th>
                <th lay-data="{field:'order_price', minWidth:120, edit:'text'}">OrderPrice</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">ShopPrice</th>
                <th lay-data="{field:'order_time', minWidth:180}">OrderTimeConsuming</th>
                <th lay-data="{field:'comment', minWidth:160}">Comment</th>
                <th lay-data="{field:'admin_username', minWidth:160}">Operator</th>
                <th lay-data="{field:'updated_at', minWidth:160}">EndTime</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>{{$order->shop->user_nick_name}}</td>
                    <td>{{$order->user_name}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>{{$status[$order->status]}}</td>
                    <td>@if(!empty($order->menu)){{$order->menu}}@endif</td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins@endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->admin_usernmae)){{$order->admin_usernmae}}@endif</td>
                    <td>{{$order->updated_at}}</td>
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
            setTimeout(function() {
                location.reload();
            }, 60000);
        });
    </script>
@endsection