@extends('layouts.app')
<style>
    table td img { max-height: 30px; max-width: 30px; }
</style>
@section('content')
        <div class="layui-fluid">
            <table class="layui-table" lay-filter="table" id="table">
                <thead>
                <tr>
                    <th lay-data="{field:'id', width:180,fixed: 'left'}">{{trans('business.table.header.order.order_id')}}</th>
                    <th lay-data="{field:'status', minWidth:120}">{{trans('business.table.header.order.status')}}</th>
                    <th lay-data="{field:'shop', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
                    <th lay-data="{field:'shop_contact', minWidth:180}">{{trans('business.table.header.shop.user_contact')}}</th>
                    <th lay-data="{field:'shop_address', minWidth:180}">{{trans('business.table.header.shop.user_address')}}</th>
                    <th lay-data="{field:'order_user_name', maxWidth:180, minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
                    <th lay-data="{field:'order_user_contact', minWidth:180}">{{trans('business.table.header.order.user_contact')}}</th>
                    <th lay-data="{field:'order_user_address', minWidth:180}">{{trans('business.table.header.order.user_address')}}</th>
                    <th lay-data="{field:'goods_id', minWidth:180}">{{trans('business.table.header.goods.id')}}</th>
                    <th lay-data="{field:'goods_name', minWidth:180}">{{trans('business.table.header.goods.name')}}</th>
                    <th lay-data="{field:'goods_price', minWidth:180}">{{trans('business.table.header.goods.price')}}</th>
                    <th lay-data="{field:'goods_number', minWidth:180}">{{trans('business.table.header.goods.number')}}</th>
                    <th lay-data="{field:'goods_image', minWidth:180}">{{trans('business.table.header.goods.image')}}</th>
                    <th lay-data="{field:'schedule', minWidth:170, event:'updateStatus'}">{{trans('business.table.header.order.schedule')}}</th>
                    <th lay-data="{field:'order_price', minWidth:120}">{{trans('business.table.header.order.order_price')}}</th>
                    <th lay-data="{field:'promo_code', minWidth:120}">{{trans('business.table.header.order.promo_code')}}</th>
                    <th lay-data="{field:'delivery_coast', minWidth:120}">{{trans('business.table.header.order.delivery_coast')}}</th>
                    <th lay-data="{field:'discount_type', minWidth:120}">{{trans('business.table.header.order.discount_type')}}</th>
                    <th lay-data="{field:'reduction', minWidth:120}">{{trans('business.table.header.order.reduction')}}</th>
                    <th lay-data="{field:'discount', minWidth:120}">{{trans('business.table.header.order.discount')}}</th>
                    <th lay-data="{field:'discounted_price', minWidth:150}">{{trans('business.table.header.order.discounted_price')}}</th>
                    <th lay-data="{field:'order_created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                    <th lay-data="{field:'order_updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->detail as $d)
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
                        <td>{{$d['id']}}</td>
                        <td>{{$d['name']}}</td>
                        <td>{{$d['price']}}</td>
                        <td>{{$d['goodsNumber']??0}}</td>
                        <td>
                            @foreach($d['image'] as $i)
                                <img src="{{$i['url']}}"/>
                            @endforeach
                        </td>
                        <td>
                            <span class="layui-bg-{{$colorStyles[$order->schedule]}} layui-btn layui-btn-sm ">{{$schedules[$order->schedule]}}</span>
                        </td>
                        <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                        <td>{{$order->promo_code}}</td>
                        <td>{{$order->delivery_coast}}</td>
                        <td>{{$order->discount_type}}</td>
                        <td>{{$order->reduction}}</td>
                        <td>{{$order->discount}}</td>
                        <td>{{$order->discounted_price}}</td>

                        <td>{{$order->created_at}}</td>
                        <td>{{$order->updated_at}}</td>
                    </tr>
                @endforeach
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
        }).use(['common', 'table' ], function () {
            const form = layui.form,
                table = layui.table,
                common = layui.common,
                $ = layui.jquery;
            table.init('table', { //转化静态表格
                page:false
            });

            $(function () {
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
        });
    </script>
@endsection