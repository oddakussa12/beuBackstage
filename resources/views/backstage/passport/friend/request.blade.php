@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('friend.form.label.from')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="" name="from" id="from"  @if(!empty($from)) value="{{$from}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('friend.form.label.to')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="" name="to" id="to"  @if(!empty($to)) value="{{$to}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="common_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:200 ,fixed: 'left'}">{{trans('friend.table.header.id')}}</th>
                <th  lay-data="{field:'from_name', minWidth:200 ,fixed: 'left'}">{{trans('friend.table.header.from_name')}}</th>
                <th  lay-data="{field:'from_nick_name', minWidth:250 ,fixed: 'left'}">{{trans('friend.table.header.from_nick_name')}}</th>
                <th  lay-data="{field:'to_name', minWidth:200}">{{trans('friend.table.header.to_name')}}</th>
                <th  lay-data="{field:'to_nick_name', minWidth:250}">{{trans('friend.table.header.to_nick_name')}}</th>
                <th  lay-data="{field:'status', width:100}">{{trans('friend.table.header.status')}}</th>
                <th  lay-data="{field:'created_at', width:200}">{{trans('friend.table.header.created_at')}}</th>
                <th  lay-data="{field:'updated_at', width:200}">{{trans('friend.table.header.updated_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{$request->request_id}}</td>
                    <td>{{$request->from->user_name}}</td>
                    <td>{{$request->from->user_nick_name}}</td>
                    <td>{{$request->to->user_name}}</td>
                    <td>{{$request->to->user_nick_name}}</td>
                    <td>
                        @if($request->request_state==0)
                            <span class="layui-badge layui-bg-gray">{{trans('friend.table.button.ignore')}}</span>
                        @endif($request->request_state==1)
                            <span class="layui-badge layui-bg-blue">{{trans('friend.table.button.accept')}}</span>
                        @else
                            <span class="layui-badge layui-bg-orange">{{trans('friend.table.button.refuse')}}</span>
                        @endif
                    </td>
                    <td>{{$request->request_created_at}}</td>
                    <td>{{$request->request_updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $requests->links('vendor.pagination.default') }}
        @else
            {{ $requests->appends($appends)->links('vendor.pagination.default') }}
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
            echarts: 'lay/modules/echarts',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'element'], function () {
            let $ = layui.jquery,
                table = layui.table;

            table.init('common_table', { //转化静态表格
                page:false
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
