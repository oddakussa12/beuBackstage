@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">

                    <div class="layui-btn-group">

                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{fixed: 'left', field:'id', width:180}">Date</th>
                <th lay-data="{field:'1', minWidth:120}">Goods name</th>
                <th lay-data="{field:'2', minWidth:120}">Shop name</th>
                <th lay-data="{field:'3', minWidth:120}">Total original price</th>
                <th lay-data="{field:'4', minWidth:120}">Discount from shops</th>
                <th lay-data="{field:'5', minWidth:120}">Sale price</th>
                <th lay-data="{field:'6', minWidth:120}">Lost/earned per order</th>
                <th lay-data="{field:'7', minWidth:120}">Numbers of special offers</th>
                <th lay-data="{field:'8', minWidth:120}">Expenses/ Income</th>
                <th lay-data="{field:'9', minWidth:120}">Total order number of the day</th>
                <th lay-data="{field:'10', minWidth:120}">Total order number of special offers</th>


                <th lay-data="{field:'11', minWidth:120}">Percentage of all special offers for the day</th>
                <th lay-data="{field:'12', minWidth:120}">Total expenses/income</th>
                <th lay-data="{field:'13', minWidth:120}">Delivery fee for all orders</th>
                <th lay-data="{field:'14', minWidth:120}">Earned/lost money</th>
                <th lay-data="{field:'15', minWidth:120}">Average total money earned/lost per order for the day</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{$order->date}}</td>
                <td>{{$order->goods_name}}</td>
                <td>{{$order->shop->user_nick_name??''}}</td>
                <td>{{$order->total_price}}</td>
                <td>{{$order->total_price-$order->total_purchase_price}}</td>
                <td>{{$order->special_price}}</td>
                <td>{{$order->num>0?round(($order->special_price-$order->total_purchase_price)/$order->num , 2):0}}</td>
                <td>{{$order->num}}</td>
                <td>{{$order->special_price-$order->total_purchase_price}}</td>

                <td>{{$order->order_count}}</td>
                <td>{{$order->today_special_order_count}}</td>

                <td>{{empty($order->order_count)?0:round($order->today_special_order_count/$order->order_count*100 , 2)}}%</td>
                <td>{{$order->expenses}}</td>
                <td>{{$order->delivery_cost_c}}</td>
                <td>{{$order->delivery_cost_c+$order->expenses}}</td>
                <td>{{empty($order->total_price_c)?0:round(($order->delivery_cost_c+$order->expenses)/$order->total_price_c , 2)}}</td>
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
        }).use(['common', 'table' , 'dropdown'], function () {
            const table = layui.table,
            dropdown = layui.dropdown,
            common = layui.common,
            form = layui.form,
            $ = layui.jquery;

        });
    </script>
    <script type="text/html" id="op">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-xs" lay-event="goods">{{trans('common.table.button.detail')}}</a>
        </div>
    </script>
@endsection