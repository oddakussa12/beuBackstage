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
                <th  lay-data="{field:'date', minWidth:120 ,fixed: 'left'}">Date</th>
                <th  lay-data="{field:'user_name', minWidth:190}">userName</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">userNickName</th>
                <th  lay-data="{field:'friend', minWidth:100}">Friend</th>
                <th  lay-data="{field:'new', minWidth:100}">New</th>
                <th  lay-data="{field:'detail', width:160}">Detail</th>
                <th  lay-data="{field:'user_created_at', width:170}">UserCreatedAt</th>
                <th  lay-data="{field:'created_at', width:160}">CreatedAt</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td>{{$user->date}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->friend}}</td>
                    <td>{{$user->new}}</td>
                    <td>{{$user->detail}}</td>
                    <td>{{$user->user_created_at}}</td>
                    <td>{{ $user->created_at }}</td>
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
