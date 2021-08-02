@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}：</label>
                    <div class="layui-input-inline">
                        <select name="order">
                            @foreach(trans('group.form.select.sort') as $k=>$v)
                            <option value="{{$k}}"  @if(!empty($order)&&$order==$k)  selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('group.form.label.administrator')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="administrator" id="administrator"  @if(!empty($administrator)) value="{{$administrator}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('group.form.label.name')}}:</label>
                    <div class="layui-input-inline" >
                        <input class="layui-input" name="name" id="name"  @if(!empty($name)) value="{{$name}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('group.form.label.group_id')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="id" id="id"  @if(!empty($id)) value="{{$id}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" >{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
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
                <th  lay-data="{field:'id', minWidth:180}">{{trans('group.table.header.group_id')}}</th>
                <th  lay-data="{field:'avatar', minWidth:200}">{{trans('group.table.header.avatar')}}</th>
                <th  lay-data="{field:'name', minWidth:200}">{{trans('group.table.header.name')}}</th>
                <th  lay-data="{field:'is_deleted', minWidth:100}">{{trans('group.table.header.is_deleted')}}</th>
                <th  lay-data="{field:'member', minWidth:120}">{{trans('group.table.header.member')}}</th>
                <th  lay-data="{field:'administrator', minWidth:150}">{{trans('group.table.header.administrator')}}</th>
                <th  lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th  lay-data="{field:'user_id', minWidth:100}">{{trans('group.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_op', minWidth:100 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>@if(is_array($group->avatar))
                            @foreach($group->avatar as $avatar)
                                @if(!empty($avatar))
                                <img width="35px" src="{{splitJointQnImageUrl($avatar)}}" />
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $group->name }}</td>
                    <td><input type="checkbox" @if(!empty($group->is_deleted)) checked @endif name="is_deleted" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO" disabled></td>
                    <td>{{ $group->member }}</td>
                    <td>{{ $group->administrator }}</td>
                    <td>{{ $group->created_at }}</td>
                    <td>{{$group->user_id}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $groups->links('vendor.pagination.default') }}
        @else
            {{ $groups->appends($appends)->links('vendor.pagination.default') }}
        @endif


    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
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
                form = layui.form,
                common = layui.common,
                flow = layui.flow,
                timePicker = layui.timePicker;
            flow.lazyimg();

            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'detail'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/passport/group')}}/"+data.id);
                }
            });
            table.init('table', { //转化静态表格
                page:false
            });

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                    locale:"{{locale()}}"
                },
            });
        })
    </script>
@endsection