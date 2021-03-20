@extends('layouts.dashboard')
@section('layui-content')
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="common_table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', minWidth:130 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'user_avatar', minWidth:80}">{{trans('user.table.header.user_gender')}}</th>
            <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_gender', minWidth:70}">{{trans('user.table.header.user_gender')}}</th>
            <th  lay-data="{field:'create_at', minWidth:160}">{{trans('user.table.header.user_friend_time')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->user_id}}</td>
                <td><img width="32px;" src="@if(stripos($user->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$user->user_avatar}}@else{{$user->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                <td>{{$user->user_nick_name}}</td>
                <td>{{$user->user_name}}</td>
                <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
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
        }).use(['common' , 'table'], function () {
            let table = layui.table;
            table.init('common_table', {
                page:false
            });
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
