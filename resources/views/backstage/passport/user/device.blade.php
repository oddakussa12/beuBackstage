@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'ip', minWidth:130 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'time', minWidth:80}">DeviceId</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->user_id}}</td>
                <td>{{$l->device_id}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'table', 'element'], function () {
            let table = layui.table;
            table.init('table', { //转化静态表格
                page:false
            });
        })
    </script>
@endsection
