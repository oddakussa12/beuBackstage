@extends('layouts.app')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>

    <div  class="layui-fluid">
        <br>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.table.header.user_name')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="keyword" id="keyword" placeholder=" fuzzy search " @if(!empty($keyword)) value="{{$keyword}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'user_id', minWidth:150}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'user_avatar', minWidth:100}">{{trans('user.table.header.user_avatar')}}</th>
                <th lay-data="{field:'user_name', minWidth:150}">{{trans('user.table.header.user_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
{{--                <th lay-data="{field:'flag', minWidth:100}">{{trans('common.table.header.status')}}</th>--}}
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->user_id}}</td>
                    <td><img width="32px;" src="{{splitJointQnImageUrl($value->user_avatar)}}" /></td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_nick_name}}</td>
{{--                    <td>{{$value->flag}}</td>--}}
                    <td>{{$value->created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $result->links('vendor.pagination.default') }}
        @else
            {{ $result->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['table' , 'layer', 'timePicker'], function () {
            const timePicker = layui.timePicker,
                table = layui.table;
            table.init('table', { //转化静态表格
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