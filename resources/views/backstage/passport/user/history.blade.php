@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="user_table">
        <thead>
        <tr>
            <th  lay-data="{field:'ip', minWidth:130 ,fixed: 'left'}">{{trans('user.table.header.user_ip_address')}}</th>
            <th  lay-data="{field:'time', minWidth:80}">{{trans('user.table.header.last_active_time')}}</th>
            <th  lay-data="{field:'status', minWidth:150}">{{trans('common.table.header.status')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->ip}}</td>
                <td>{{date('Y-m-d H:i:s', $user->time)}}</td>
                <td><span class="layui-btn layui-btn-xs @if($user->status==1) layui-btn-danger @endif">@if($user->status==1) Online @else Offline @endif</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $users->links('vendor.pagination.default') }}
    @else
        {{ $users->appends($appends)->links('vendor.pagination.default') }}
    @endif


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
            let table = layui.table,
                element = layui.element;
            table.init('user_table', { //转化静态表格
                page:false
            });
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
