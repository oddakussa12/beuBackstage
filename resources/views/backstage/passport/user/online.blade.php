@extends('layouts.dashboard')
@section('layui-content')
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">Name/ID:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                </div>
            </div>
        </div>
    </form>
    <table class="layui-table"  lay-filter="user_table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
            <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>
            <th  lay-data="{field:'time', width:160,sort:true}">{{trans('user.table.header.last_active_time')}}</th>
            <th  lay-data="{field:'ip', width:160}">{{trans('user.table.header.last_active_ip')}}</th>
            <th  lay-data="{field:'activation', width:70}">{{trans('user.table.header.user_activation')}}</th>
            <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
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
                <td>{{ $user->country }}</td>
                <td>{{$user->activeTime}}</td>
                <td>{{$user->ip}}</td>
                <td><span class="layui-btn layui-btn-xs">@if($user->activation==1) YES @else NO @endif</span></td>
                <td>{{ $user->user_format_created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $users->links('vendor.pagination.default') }}
    @else
        {{ $users->appends($appends)->links('vendor.pagination.default') }}
    @endif
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-container">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="friend">{{trans('user.table.button.friend')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="history">{{trans('user.table.button.history')}}</a>
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
                laydate = layui.laydate,
                flow = layui.flow,
                timePicker = layui.timePicker;

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });
            table.init('user_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
