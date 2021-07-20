@extends('layouts.dashboard')
@section('layui-content')
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                </div>
                <div class="layui-input-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </div>
    </form>
    <table class="layui-table"  lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'follow_id', width:110 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'follow_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'follow_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'follow_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>

            <th  lay-data="{field:'followed_id', width:110}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'followed_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'followed_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'followed_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($follows as $follow)
            <tr>
                <td>{{$follow->follower->user_id}}</td>
                <td><img width="32px;" src="@if(stripos($follow->follower->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$follow->follower->user_avatar}}@else{{$follow->follower->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                <td>{{$follow->follower->user_nick_name}}</td>
                <td>{{$follow->follower->user_name}}</td>
                <td>{{$follow->followeder->user_id}}</td>
                <td><img width="32px;" src="@if(stripos($follow->followeder->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$follow->followeder->user_avatar}}@else{{$follow->followeder->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                <td>{{$follow->followeder->user_nick_name}}</td>
                <td>{{$follow->followeder->user_name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($follows))
        {{ $follows->links('vendor.pagination.default') }}
    @else
        {{ $follows->appends($appends)->links('vendor.pagination.default') }}
    @endif
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="device">Device</a>
            <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="friend">{{trans('user.table.button.friend')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="history">{{trans('user.table.button.history')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="friend_active">{{trans('user.table.button.friend_active')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="friend_yesterday_active">{{trans('user.table.button.friend_yesterday_active')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
            echarts: 'lay/modules/echarts',
        }).use(['element', 'common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker', 'echarts'], function () {
            let $ = layui.jquery,
                element = layui.element,
                table = layui.table,
                common = layui.common,
                echarts = layui.echarts,
                form = layui.form,
                flow = layui.flow,
                timePicker = layui.timePicker;

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                    locale:"{{locale()}}"
                },
            });

            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        })
    </script>
@endsection
