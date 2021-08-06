@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-fluid">
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'promo_code', minWidth:130}">{{trans('business.table.header.promo_code.promo_code')}}</th>
                <th lay-data="{field:'count', minWidth:80}">{{trans('business.table.header.promo_code.count')}}</th>
                <th lay-data="{fixed: 'right', width:80, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ranks as $value)
                <tr>
                    <td>{{$value->promo_code}}</td>
                    <td>{{$value->num}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table'], function () {
            const table = layui.table,
                common = layui.common;
            table.init('table', {
                page:false,
                toolbar: '#toolbar'
            });
            table.on('tool(table)', function(obj){
                let data = obj.data;
                let layEvent = obj.event;
                if(layEvent === 'detail'){
                    window.open("{{LaravelLocalization::localizeUrl('backstage/business/complex')}}/"+"?type=shop_order&promo_code="+data.promo_code);
                }
            });

        });
    </script>
    <script type="text/html" id="op">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        </div>
    </script>
@endsection