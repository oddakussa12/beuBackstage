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
                        <a href="?schedule={{$schedule}}&admin_id=0" class="layui-btn @if(isset($admin_id)&&$admin_id==0) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">All</a>
                        @foreach($admins as $admin)
                            <a href="?schedule={{$schedule}}&admin_id={{$admin->admin_id}}" class="layui-btn @if(isset($admin_id)&&$admin_id==$admin->admin_id) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{$admin->admin_username}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">

                    <div class="layui-btn-group">
                        <a href="?schedule=0&admin_id={{$admin_id}}" class="layui-btn @if(isset($schedule)&&$schedule=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">All</a>
                        @foreach($schedules as $key=>$value)
                            @if($key>=5)
                                <a href="?schedule={{$key}}&admin_id={{$admin_id}}" class="layui-btn @if(isset($schedule)&&$schedule==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{$value}}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180}">{{trans('business.table.header.order.order_id')}}</th>
                <th lay-data="{field:'status', minWidth:120}">{{trans('business.table.header.order.status')}}</th>
                <th lay-data="{field:'shop_name', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'shop_contact', minWidth:180}">{{trans('business.table.header.shop.user_contact')}}</th>
                <th lay-data="{field:'shop_address', minWidth:180}">{{trans('business.table.header.shop.user_address')}}</th>
                <th lay-data="{field:'user_name', maxWidth:180, minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
                <th lay-data="{field:'user_contact', minWidth:180}">{{trans('business.table.header.order.user_contact')}}</th>
                <th lay-data="{field:'user_address', minWidth:180}">{{trans('business.table.header.order.user_address')}}</th>
                <th lay-data="{field:'schedule', minWidth:170, event:'updateStatus'}">{{trans('business.table.header.order.schedule')}}</th>
                <th lay-data="{field:'promo_code', minWidth:120}">{{trans('business.table.header.order.promo_code')}}</th>
                <th lay-data="{field:'delivery_coast', minWidth:120}">{{trans('business.table.header.order.delivery_coast')}}</th>
                <th lay-data="{field:'discount_type', minWidth:120}">{{trans('business.table.header.order.discount_type')}}</th>
                <th lay-data="{field:'reduction', minWidth:120}">{{trans('business.table.header.order.reduction')}}</th>
                <th lay-data="{field:'discount', minWidth:120}">{{trans('business.table.header.order.discount')}}</th>
                <th lay-data="{field:'brokerage_percentage', minWidth:120}">{{trans('business.table.header.order.brokerage_percentage')}}</th>
                <th lay-data="{field:'brokerage', minWidth:120}">{{trans('business.table.header.order.brokerage')}}
                <th lay-data="{field:'free_delivery', minWidth:100}">{{trans('business.table.header.order.free_delivery')}}</th>
                <th lay-data="{field:'comment', minWidth:160}">{{trans('business.table.header.order.comment')}}</th>
                <th lay-data="{field:'order_price', minWidth:150}">{{trans('business.table.header.order.order_price')}}</th>
                <th lay-data="{field:'discounted_price', minWidth:150}">{{trans('business.table.header.order.discounted_price')}}</th>
                <th lay-data="{field:'profit', minWidth:150}">{{trans('business.table.header.order.profit')}}</th>
                <th lay-data="{field:'order_time', minWidth:180}">{{trans('business.table.header.order.order_time_consuming')}}</th>
                <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
                <th lay-data="{field:'order_created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'order_updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:200, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>
                    <span class="layui-btn layui-btn-sm @if($order->status==1) layui-bg-green @elseif($order->status==2) layui-bg-gray @else layui-btn-warm @endif">
                        @if($order->status==1) Completed @elseif($order->status==2) Canceled @else InProcess @endif
                    </span>
                    </td>
                    <td>@if(!empty($order->shop->user_nick_name)){{$order->shop->user_nick_name}}@endif</td>
                    <td>@if(!empty($order->shop->user_contact)){{$order->shop->user_contact}}@endif</td>
                    <td>@if(!empty($order->shop->user_address)){{$order->shop->user_address}}@endif</td>
                    <td>{{$order->user_name}}</td>
                    <td>@if(!empty($order->user_contact)){{$order->user_contact}}@endif</td>
                    <td>{{$order->user_address}}</td>
                    <td>
                        <span class="layui-bg-{{$colorStyles[$order->schedule]}} layui-btn layui-btn-sm ">{{$schedules[$order->schedule]}}</span>
                    </td>
                    <td>{{$order->promo_code}}</td>
                    <td>{{$order->delivery_coast}}</td>
                    <td>{{$order->discount_type}}</td>
                    <td>{{$order->reduction}}</td>
                    <td>{{$order->discount}}</td>
                    <td>{{$order->brokerage_percentage}}</td>
                    <td>@if(!empty($order->brokerage)){{$order->brokerage}}@endif</td>
                    <td><input type="checkbox" @if($order->free_delivery==true) checked @endif name="free_delivery" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO" disabled></td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>{{$order->order_price}}</td>
                    <td>{{$order->discounted_price}}</td>
                    <td>{{$order->profit}}</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                    <td>@if(!empty($order->color)){{$order->color}}@endif</td>
                    <td>{{$order->created_at}}</td>
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


        <table class="layui-table" lay-filter="statistics">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th lay-data="{field:'discounted_price', minWidth:180}">All the money received</th>
                    <th lay-data="{field:'shop_price', minWidth:180}">Money for the store</th>
                    <th lay-data="{field:'delivery_coast', minWidth:180}">Delivery coast</th>
                    <th lay-data="{field:'shop_price', minWidth:180}">Gross profit</th>
                <tr>
            </thead>
            <tbody>
                <tr>
                    <td>@if(!empty($money['discounted_price'])){{$money['discounted_price']}}@else 0 @endif</td>
                    <td>@if(!empty($money['delivery_coast'])){{$money['delivery_coast']}}@else 0 @endif</td>
                    <td>@if(!empty($money['brokerage'])){{$money['brokerage']}}@else 0 @endif</td>
                    <td>{{$money['discounted_price']-$money['brokerage']}}</td>
                </tr>
            </tbody>
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
        }).use(['common', 'table'], function () {
            const table = layui.table,
            common = layui.common;
            table.init('table', {
                page:false
            });
            table.init('statistics', {
                page:false
            });
            table.on('tool(table)', function (obj) {
                var data = obj.data;
                if (obj.event === 'goods') {
                    open('/backstage/business/shop_order/'+data.id);
                }
            });
            function open(content , type=2) {
                var clientWidth = document.body.clientWidth;
                if(clientWidth<=600)
                {
                    var area = ['90%','90%'];
                }else if(clientWidth<=1200&&clientWidth>600){
                    var area = ['72%','90%'];
                }else{
                    var area = ['80%','90%'];
                }
                common.open(content , {
                    shadeClose: false,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    scrollbar:true,
                } , type);
            }
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="goods">Goods</a>
    </script>
@endsection