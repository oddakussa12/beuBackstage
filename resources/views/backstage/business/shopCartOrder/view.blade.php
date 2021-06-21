@extends('layouts.app')
    <style>
        .layui-table-select-dl { color: black}
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
                <th lay-data="{field:'goods_id', minWidth:180, hide:'true'}">{{trans('business.table.header.goods_id')}}</th>
                <th lay-data="{field:'goods_name', minWidth:160}">{{trans('business.table.header.goods_name')}}</th>
                <th lay-data="{field:'goods_image', minWidth:200}">{{trans('common.table.header.image')}}</th>
                <th lay-data="{field:'goods_price', minWidth:140}">{{trans('business.table.header.price')}}</th>
                <th lay-data="{field:'goods_num', minWidth:140}">{{trans('business.table.header.goods_num')}}</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($result))
                @foreach($result as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>@if(!empty($value['image']))
                                @foreach($value['image'] as $image)
                                    <img src="{{$image['url']}}">
                                @endforeach
                            @endif
                        </td>
                        <td>@if(!empty($value['price'])){{$value['price']}}@endif</td>
                        <td>@if(!empty($value['goodsNumber'])){{$value['goodsNumber']}}@endif</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
@section('footerScripts')
    @parent
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="goods">Goods</a>
    </script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).use(['table'], function () {
            const table = layui.table;
            table.init('table', { //转化静态表格
                page:false,
            });
        });
    </script>
@endsection