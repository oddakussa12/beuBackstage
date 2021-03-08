@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="user_table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', minWidth:130 ,fixed: 'left'}">用户{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'user_avatar', minWidth:80}">头像</th>
            <th  lay-data="{field:'user_nick_name', minWidth:150}">用户昵称</th>
            <th  lay-data="{field:'user_name', minWidth:190}">用户名</th>
            <th  lay-data="{field:'user_gender', minWidth:70}">性别</th>
            <th  lay-data="{field:'create_at', minWidth:160}">加好友时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->user_id}}</td>
                <td><img width="32px;" src="@if(stripos($user->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$user->user_avatar}}@else{{$user->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                <td>{{$user->user_nick_name}}</td>
                <td>{{$user->user_name}}</td>
                <td><span class="layui-btn layui-btn-xs">@if($user->user_gender==-1)未知@elseif($user->user_gender==0)女@else男@endif</span></td>
                <td>{{date('Y-m-d H:i:s', $user->created_at)}}</td>
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
