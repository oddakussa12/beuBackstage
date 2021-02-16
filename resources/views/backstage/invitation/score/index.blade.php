@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .left div {float: left; width:10%;padding:20px;border:0px solid #01AAED;}
        .left div span {color: #aa4a24; font-size: 14px;}
    </style>
    <div  class="layui-fluid">
        <div class="left">
            <div><span>累计发放积分：{{$total['totalScore']}}</span></div>
            <div><span>累计积分消耗：{{$total['usedScore']}}</span></div>
            <div><span>累计邀请新用户：{{$total['userCount']}}</span></div>
        </div>
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">用户ID:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_id" id="user_id" @if(!empty($user_id)) value="{{$user_id}}" @endif/>
                </div>
                <label class="layui-form-label">用户名:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_name" id="user_name" @if(!empty($user_name)) value="{{$user_name}}" @endif/>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="post_table" id="post_table" >
            <thead>
            <tr>
                <th lay-data="{field:'user_id', width:100}">用户ID</th>
                <th lay-data="{field:'user_avatar', width:70}">头像</th>
                <th lay-data="{field:'user_nick_name', width:120, sort:true}">用户昵称</th>
                <th lay-data="{field:'user_name', width:120, sort:true}">用户名称</th>
                <th lay-data="{field:'user_phone', width:160}">手机号</th>
                <th lay-data="{field:'score', width:110}">当前积分</th>
                <th lay-data="{field:'score', width:110}">历史总积分</th>
                <th lay-data="{field:'used_score', width:110}">历史总消耗</th>
                <th lay-data="{field:'code', width:110}">邀请码</th>
                <th lay-data="{field:'used_num', width:90}">邀请人数</th>
                <th lay-data="{fixed: 'right', minWidth:160, align:'center', toolbar: '#postop'}">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->user_id}}</td>
                    <td><img style="height:40px; width:40px;" src="{{config('common.qnUploadDomain.avatar_domain')}}/{{$value->user_avatar}}?imageView2/5/w/50/h/50/interlace/1|imageslim"></td>
                    <td>{{$value->user_nick_name}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>+{{$value->user_phone_country}} {{$value->user_phone}}</td>
                    <td>{{$value->score}}</td>
                    <td>{{$value->score+$value->used_score}}</td>
                    <td>{{$value->used_score}}</td>
                    <td>{{$value->code}}</td>
                    <td>{{$value->used_num}}</td>
                    <td></td>
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
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker;
            table.on('tool(post_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
               if(layEvent === 'score'){ //积分明细
                    console.log(data);
                    var id = data.user_id;
                    layer.open({
                        type: 2,
                        title: '积分明细',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['60%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/invitation/score/score/'+id,
                    });
                }
                if(layEvent === 'invitation'){ //邀请明细
                    console.log(data);
                    var id = data.user_id;
                    layer.open({
                        type: 2,
                        title: '邀请明细',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['60%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/invitation/score/invite/'+id,
                    });
                }
            });
            table.init('post_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });
        });
    </script>
@endsection
<script type="text/html" id="postop">
    {{--<a class="layui-btn layui-btn-xs" lay-event="check">{{trans('common.table.button.check')}}</a>--}}
    <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="score">积分明细</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="invitation">邀请明细</a>

    {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>--}}
</script>