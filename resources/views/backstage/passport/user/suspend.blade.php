@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table td span{ height: 30px; line-height: 30px;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">被举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_id" id="user_id"  @if(!empty($user_id)) value="{{$user_id}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="user_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', minWidth:100 ,fixed: 'left'}">ID</th>
                <th  lay-data="{field:'user_id', minWidth:100 ,fixed: 'left'}">被封号ID</th>
                <th  lay-data="{field:'desc', minWidth:190}">原因</th>
                <th  lay-data="{field:'start_time', minWidth:160}">开始时间</th>
                <th  lay-data="{field:'end_time', width:160}">结束时间</th>
                <th  lay-data="{field:'operator', width:100}">封号人</th>
                <th  lay-data="{field:'created_at', width:160}">封号时间</th>
                <th  lay-data="{field:'is_delete', width:120}">是否解封</th>
                <th  lay-data="{field:'unoperator', width:100}">解封人</th>
                <th  lay-data="{field:'updated_at', width:160}">解封时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><a target="_blank" href="{{route('passport::user.index')}}?field=user_id&value={{$user->user_id}}">{{$user->id}}</a></td>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$user->user_id}}">{{$user->user_id}}</a></td>
                    <td>{{$user->desc}}</td>
                    <td>@if(!empty($user->start_time)){{date('Y-m-d H:i:s', $user->start_time)}}@endif</td>
                    <td>@if(!empty($user->end_time))  {{date('Y-m-d H:i:s', $user->end_time)}}  @endif</td>
                    <td>{{ $user->operator }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>@if(!empty($user->is_delete)) <span class="layui-btn layui-btn-warm">已解封</span> @else <span class="layui-btn">未解封</span>@endif</td>
                    <td>{{$user->unoperator}}</td>
                    <td>{{$user->updated_at}}</td>
{{--                    <td>@if(!empty($user->is_delete)){{date('Y-m-d H:i:s', $user->updated_at)}}@endif</td>--}}
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
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="password">{{trans('user.table.button.password')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="fan">{{trans('user.table.button.fan')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                timePicker = layui.timePicker;

            table.init('user_table', { //转化静态表格
                page:false
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
