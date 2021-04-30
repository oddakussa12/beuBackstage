@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
         table td { height: 40px; line-height: 40px;}
        table td img { width: 60px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">UserId:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="keyword" placeholder="user_name" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$posts->total()}}</div>
                </div>
            </div>
        </form>
<!--        <div class="layui-btn-container">
{{--                        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>--}}
            <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>
            <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>
            <button class="layui-btn layui-btn-danger" lay-event="del">删除</button>
            <button class="layui-btn layui-btn-warm" lay-event="recover">取消删除</button>
        </div>-->
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'admin_realname', minWidth:180}">姓名</th>
                <th lay-data="{field:'admin_username', minWidth:160}">昵称</th>
                <th lay-data="{field:'admin_status', minWidth:100}">状态</th>
                <th lay-data="{field:'todayClaim', minWidth:100}">今日领取数</th>
                <th lay-data="{field:'today', minWidth:100}">今日审核数</th>
                <th lay-data="{field:'monthClaim', minWidth:100}">本月领取数</th>
                <th lay-data="{field:'month', minWidth:100}">本月审核数</th>
                <th lay-data="{field:'totalClaim', minWidth:100}">总领取数</th>
                <th lay-data="{field:'total', minWidth:160}">总审核数</th>
                <th lay-data="{field:'refuseMonth', minWidth:160}">本月不通过总数</th>
                <th lay-data="{field:'refuse', minWidth:160}">不通过总数</th>
                <th lay-data="{field:'created_at', minWidth:160}">最后审核时间</th>
                <th lay-data="{fixed: 'right', minWidth:200, align:'center', toolbar: '#postop'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($posts as $value)
                <tr>
                    <td>{{$value->admin_realname}}</td>
                    <td>{{$value->admin_username}}</td>
                    <td>{{$value->admin_status}}</td>
                    <td>{{$value->todayClaim}}</td>
                    <td>{{$value->today}}</td>
                    <td>{{$value->monthClaim}}</td>
                    <td>{{$value->month}}</td>
                    <td>{{$value->total}}</td>
                    <td>{{$value->refuseMonth}}</td>
                    <td>{{$value->refuse}}</td>
                    <td>{{$value->lastTime}}</td>
                   <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $posts->links('vendor.pagination.default') }}
        @else
            {{ $posts->appends($appends)->links('vendor.pagination.default') }}
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
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;

            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        });

    </script>
@endsection