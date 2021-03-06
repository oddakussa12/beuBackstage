@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <select name="field">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach(trans('user.form.select.query') as $key=>$translation)
                            <option value="{{$key}}" @if(!empty($field)&&$field==$key) selected @endif>{{$translation}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">:</div>

                <div class="layui-input-inline">
                    <input class="layui-input" name="value" id="value"  @if(!empty($value)) value="{{$value}}" @endif />
                </div>
            </div>

            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('comment.form.placeholder.comment_country_id')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="country_code" lay-verify="" lay-search  style="width: 500px;">
                            <option value="">{{trans('comment.form.placeholder.comment_country_id')}}</option>
                            @foreach($counties  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
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
                    <a href="{{route('passport::user.export')}}@if(!empty($query))?{{$query}}@endif" class="layui-btn" target="_blank">{{trans('common.form.button.export')}}</a>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="user_table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', width:130 ,fixed: 'left'}">用户{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_avatar', width:80}">头像</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">用户昵称</th>
                <th  lay-data="{field:'user_name', minWidth:190}">用户名</th>
                <th  lay-data="{field:'user_gender', width:100}">性别</th>
                <th  lay-data="{field:'country', width:100}">国家</th>
                <th  lay-data="{field:'time', width:160,sort:true}">最近登录</th>
                <th  lay-data="{field:'ip', width:160}">最近登录IP</th>

                <th  lay-data="{field:'activation', width:100}">激活</th>
                <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
{{--                <th  lay-data="{field:'user_op', minWidth:100 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>--}}
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
                    <td>{{ $user->country }}</td>
                    <td>@if($user->time){{ date('Y-m-d H:i:s', $user->time) }}@endif</td>
                    <td>{{$user->ip}}</td>
                    <td><span class="layui-btn layui-btn-xs">@if($user->activation==0) 是 @else 否 @endif</span></td>
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


    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            let table = layui.table,
                flow = layui.flow,
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

            table.on('tool(user_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'edit'){ //编辑
                    location.href='/{{app()->getLocale()}}/backstage/passport/user/'+data.user_id+'/edit';
                }
            });

            flow.lazyimg();

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
