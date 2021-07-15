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
                        <a href="?status={{$status}}&admin_id=0" class="layui-btn @if(isset($admin_id)&&$admin_id==0) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($admins as $admin)
                            <a href="?status={{$status}}&admin_id={{$admin->admin_id}}" class="layui-btn @if(isset($admin_id)&&$admin_id==$admin->admin_id) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{$admin->admin_username}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">

                    <div class="layui-btn-group">
                        <a href="?status=0&admin_id={{$admin_id}}" class="layui-btn @if(isset($status)&&$status=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($statuses as $key=>$value)
                            @if($key>=5)
                                <a href="?status={{$key}}&admin_id={{$admin_id}}" class="layui-btn @if(isset($status)&&$status==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.'.$value)}}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">{{trans('business.table.header.order.order_id')}}</th>
                <th lay-data="{field:'shop_name', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'user_name', minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
                <th lay-data="{field:'order_status', minWidth:180}">{{trans('business.table.header.order.schedule')}}</th>
                <th lay-data="{field:'order_menu', minWidth:200}">{{trans('business.table.header.discovery_order.menu')}}</th>
                <th lay-data="{field:'order_price', minWidth:120}">{{trans('business.table.header.order.order_price')}}</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">{{trans('business.table.header.discovery_order.shop_price')}}</th>
                <th lay-data="{field:'order_time', minWidth:180}">{{trans('business.table.header.order.order_time_consuming')}}</th>
                <th lay-data="{field:'comment', minWidth:160}">{{trans('business.table.header.discovery_order.comment')}}</th>
                <th lay-data="{field:'admin_username', minWidth:100}">Operator</th>
                <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
                <th lay-data="{field:'order_created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:160}">{{trans('common.table.header.updated_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>@if(!empty($order->shop->user_nick_name)){{$order->shop->user_nick_name}}@endif</td>
                    <td>{{$order->user_name}}</td>
                    <td><span class="layui-bg-{{$colorStyles[$order->status]}} layui-btn layui-btn-sm ">{{trans('business.table.header.shop_order.'.$statuses[$order->status])}}</span></td>
                    <td>@if(!empty($order->menu)){{$order->menu}}@endif</td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->operator)){{$order->operator->admin_username}}@endif</td>
                    <td>@if(!empty($order->color)){{$order->color}}@endif</td>
                    <td>{{$order->created_at}}</td>
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
            const table = layui.table,
            $ = layui.jquery;
            table.init('table', {
                page:false,
                done: function(res, curr, count){
                    console.log(res.data);
                    $('th').css({'font-size': '15'});	//进行表头样式设置
                    for(var i in res.data){		//遍历整个表格数据
                        var item = res.data[i];		//获取当前行数据
                        if(item.color==1){
                            $("tr[data-index='" + i + "']").attr({"style":"background:#ff3333; color:#fff"});  //将当前行变成绿色
                        }
                    }
                }
            });

            setTimeout(function() {
                location.reload();
            }, 300000);
        });
    </script>
@endsection