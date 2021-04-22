@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">UserID:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', minWidth:120 ,fixed: 'left'}">ID</th>
                <th  lay-data="{field:'user_avatar', width:80}">Avatar</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">userNickName</th>
                <th  lay-data="{field:'user_name', minWidth:190}">userName</th>
                <th  lay-data="{field:'desc', minWidth:150, hide:true}">Reason</th>
                <th  lay-data="{field:'start_time', minWidth:160}">StartTime</th>
                <th  lay-data="{field:'end_time', width:160}">EndTime</th>
                <th  lay-data="{field:'operator', width:100}">Operator</th>
                <th  lay-data="{field:'created_at', width:160}">CreatedAt</th>
                <th  lay-data="{field:'is_delete', width:130}">IsBlock</th>
                <th  lay-data="{field:'unoperator', width:130}">UnOperator</th>
                <th  lay-data="{field:'updated_at', width:160}">UpdatedAt</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td><img width="32px;" src="@if(stripos($user->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$user->user_avatar}}@else{{$user->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->desc}}</td>
                    <td>@if(!empty($user->start_time)){{$user->start_time}}@endif</td>
                    <td>@if(!empty($user->end_time))  {{$user->end_time}}  @endif</td>
                    <td>{{ $user->operator }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>@if(!empty($user->is_delete)) <span class="layui-btn layui-btn-xs layui-btn-warm">NO</span> @else <span class="layui-btn layui-btn-xs layui-btn-normal">YES</span>@endif</td>
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

            table.init('table', { //转化静态表格
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
@endsection
