@extends('layouts.app')
    <div class="layui-fluid">
        <br>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
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
                <th lay-data="{field:'goods_name', minWidth:180}">{{trans('business.table.header.goods_name')}}</th>
                <th lay-data="{field:'user_name', minWidth:180}">{{trans('user.table.header.user_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:180 , hide:true}">{{trans('user.table.header.user_nick_name')}}</th>
                <th lay-data="{field:'shop_name', minWidth:180}">{{trans('business.table.header.shop_name')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:180 , hide:true}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($views as $value)
                <tr>
                    <td>{{$value->goods_name}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_nick_name}}</td>
                    <td>@if(isset($value->shop_name)){{$value->shop_name}}@endif</td>
                    <td>@if(isset($value->shop_nick_name)){{$value->shop_nick_name}}@endif</td>
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
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const table = layui.table,
                timePicker = layui.timePicker;
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