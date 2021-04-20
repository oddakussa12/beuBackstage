@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'role', minWidth:150}">Type</th>
            <th  lay-data="{field:'created_at', minWidth:160}">{{trans('user.table.header.user_registered')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->user_id}}</td>
                <td><img width="32px;" src="@if(stripos($user->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$user->user_avatar}}@else{{$user->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                <td>{{$user->user_nick_name}}</td>
                <td>{{$user->user_name}}</td>
                <td>@if($user->role==1) manager @else normal @endif</td>
                <td>{{$user->created_at }}</td>
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
            let table = layui.table;
            table.init('table', { //转化静态表格
                page:false
            });
        })
    </script>
@endsection
