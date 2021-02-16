@extends('layouts.app')
@section('content')
    <div  class="layui-fluid">
{{--        <fieldset class="layui-elem-field layui-field-title">--}}
{{--            <legend>热门话题</legend>--}}
{{--        </fieldset>--}}
        <table class="layui-table"   lay-filter="post_table" id="post_table" >
            <thead>
            <tr>
                <th lay-data="{field:'user_id', width:100}">新邀用户ID</th>
                <th lay-data="{field:'score', width:110}">用户昵称</th>
                <th lay-data="{field:'type', width:110}">用户名</th>
                <th lay-data="{field:'created_at', width:160}">邮箱</th>
                <th lay-data="{field:'user_ip_address', width:160}">ip</th>
                <th lay-data="{field:'user_created_at', width:160}">注册时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->user_id}}</td>
                    <td>{{$value->user_nick_name}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_email}}</td>
                    <td>{{$value->user_ip_address}}</td>
                    <td>{{$value->user_created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $data->links('vendor.pagination.default') }}
        @else
            {{ $data->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
@endsection
<script type="text/html" id="postop">
    {{--<a class="layui-btn layui-btn-xs" lay-event="check">{{trans('common.table.button.check')}}</a>--}}
    <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="score">积分明细</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="invitation">邀请明细</a>

    {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>--}}
</script>