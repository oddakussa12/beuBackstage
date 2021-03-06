@extends('layouts.app')
    <div class="layui-fluid">
        <br>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>

        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'referrer', minWidth:80 , fixed:'left'}">{{trans('user.table.header.user_src')}}</th>
                <th lay-data="{field:'user_name', minWidth:160}">{{trans('user.table.header.user_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('user.table.header.user_nick_name')}}</th>
                <th lay-data="{field:'created_at', width:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($views as $value)
                <tr>
                    <td>{{$value->referrer}}</td>
                    <td>{{$value->user->user_name}}</td>
                    <td>{{$value->user->user_nick_name}}</td>
                    <td>{{$value->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $views->links('vendor.pagination.default') }}
        @else
            {{ $views->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@section('footerScripts')
    @parent
    <script>
        //??????????????????
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const table = layui.table,
                timePicker = layui.timePicker;
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}"
                },
            });
        });
    </script>
@endsection