@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">排序：</label>
                <div class="layui-input-inline">
                    <select name="order">
                        <option value=""  @if(empty($field))  selected @endif>时间</option>
                        <option value="1" @if(!empty($order)) selected @endif>次数</option>
                    </select>
                </div>
                <label class="layui-form-label">处理状态：</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="0" @if(isset($status) && $status==0)  selected @endif>未处理</option>
                        <option value="1" @if(!empty($status)) selected @endif>已处理</option>
                        <option value="" @if(!isset($status))  selected @endif>全部</option>
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_id" id="user_id"  @if(!empty($user_id)) value="{{$user_id}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">被举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="reportable_id" id="reportable_id"  @if(!empty($reportable_id)) value="{{$reportable_id}}" @endif />
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                    <div class="layui-input-inline" style="width: 290px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <a href="{{route('passport::user.export')}}@if(!empty($query))?{{$query}}@endif" class="layui-btn" target="_blank">{{trans('common.form.button.export')}}</a>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="user_table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', minWidth:100 ,fixed: 'left'}">用户{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150 ,fixed: 'left'}">用户昵称</th>
                <th  lay-data="{field:'user_name', minWidth:190}">用户名</th>
                <th  lay-data="{field:'user_email', minWidth:200}">{{trans('user.table.header.user_email')}}</th>
                <th  lay-data="{field:'num_all', width:100}">被举报次数</th>
                <th  lay-data="{field:'created_at', width:160}">第一次被举报时间</th>
                <th  lay-data="{field:'user_level', width:100}">{{trans('user.table.header.user_level')}}</th>
                <th  lay-data="{field:'user_is_block', width:100}">{{trans('user.table.header.user_block')}}</th>
                <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
                <th  lay-data="{field:'user_country', width:150}">{{trans('user.table.header.user_country_id')}}</th>
                <th  lay-data="{field:'user_ip_address', width:150}">{{trans('user.table.header.user_ip_address')}}</th>
                <th  lay-data="{field:'user_op', minWidth:200 ,fixed: 'right'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$user->user_id}}">{{$user->user_id}}</a></td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_email}}</td>
                    <td>{{ $user->num_all }}</td>
                    <td>{{ $user->created_at }}</td>

                    <td><input type="checkbox" @if($user->user_level>0) checked @endif name="user_level" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><input type="checkbox" @if($user->is_block==true) checked @endif name="is_block" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{ $user->user_format_created_at }}</td>
                    <td>{{ $user->user_country }}</td>
                    <td>{{ $user->user_ip_address }}</td>

                    <td>
                        <div class="layui-table-cell laytable-cell-1-6">
                            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="people" target="_blank" href="{{route('report::report.history')}}?details=1&type=user&reportable_id={{$user->reportable_id}}">被举报明细</a>
                            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="people" target="_blank" href="{{route('report::report.reportUser')}}?reportable_id={{$user->reportable_id}}">举报人</a>
                        </div>
                    </td>
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
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                flow = layui.flow,
                timePicker = layui.timePicker

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

            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var userId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                var name = $(data.elem).attr('name');
                if(name=='is_block')
                {
                    @if(!Auth::user()->can('passport::user.block'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                    @endif
                }else{
                    @if(!Auth::user()->can('passport::user.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                    @endif
                }
                if(checked)
                {
                    var params = '{"'+name+'":"on"}';
                    var event = 'block';
                }else{
                    var event = 'unblock';
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                if(name=='is_block')
                {
                    var url = "{{url('/backstage/passport/user')}}/"+userId+'/'+event;
                }else{
                    var url = "{{url('/backstage/passport/user')}}/"+userId;
                }
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax(url , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });
            flow.lazyimg();
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
