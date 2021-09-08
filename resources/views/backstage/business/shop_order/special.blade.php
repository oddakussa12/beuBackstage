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
                <th lay-data="{fixed: 'left', field:'id', width:180}">date</th>
                <th lay-data="{field:'1', minWidth:120}">goodsName</th>
                <th lay-data="{field:'2', minWidth:120}">shopName</th>
                <th lay-data="{field:'3', minWidth:120}">A</th>
                <th lay-data="{field:'4', minWidth:120}">B</th>
                <th lay-data="{field:'5', minWidth:120}">C</th>
                <th lay-data="{field:'6', minWidth:120}">D</th>
                <th lay-data="{field:'7', minWidth:120}">E</th>
                <th lay-data="{field:'8', minWidth:120}">F</th>
                <th lay-data="{field:'9', minWidth:120}">G</th>
                <th lay-data="{field:'10', minWidth:120}">H</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{$order->date}}</td>
                <td>{{$order->goods_name}}</td>
                <td>shop name</td>
                <td>{{$order->total_price}}</td>
                <td>{{$order->total_price-$order->total_purchase_price}}</td>
                <td>{{$order->special_price}}</td>
                <td>{{$order->num>0?round(($order->special_price-$order->total_price+$order->total_purchase_price)/$order->num , 2):0}}</td>
                <td>{{$order->num}}</td>
                <td>{{$order->special_price-$order->total_price+$order->total_purchase_price}}</td>
                <td>{{$order->order_count}}</td>
                <td>{{$order->today_order_count}}</td>
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