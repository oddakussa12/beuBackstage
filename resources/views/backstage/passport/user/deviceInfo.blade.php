@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table th{ font-size: 12px; height: 40px; word-break: break-all;}
    </style>
    <div  class="layui-fluid">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'package_name', minWidth:110}">包名</th>
                <th  lay-data="{field:'install_referrer', minWidth:300}">已安装软件包的引荐来源网址</th>
                <th  lay-data="{field:'referrer_click_timestamp_seconds', minWidth:250}">引荐来源网址点击事件发生时的客户端时间</th>
                <th  lay-data="{field:'install_begin_timestamp_seconds', minWidth:250}">应用安装开始时的客户端时间</th>
                <th  lay-data="{field:'referrer_click_timestamp_server_seconds', minWidth:250}">引荐来源网址点击事件发生时的服务器端时间</th>
                <th  lay-data="{field:'install_begin_timestamp_server_seconds', minWidth:250}">应用安装开始时的服务器端时间</th>
                <th  lay-data="{field:'install_version', minWidth:250}">首次安装应用时的应用版本</th>
                <th  lay-data="{field:'google_play_instant', minWidth:250}">表明应用的免安装体验是否为过去7天内发布的</th>
                <th  lay-data="{field:'app_version', minWidth:110}">APP版本</th>
                <th  lay-data="{field:'device_id', minWidth:250}">设备ID</th>
                <th  lay-data="{field:'brand', minWidth:120}">手机品牌</th>
                <th  lay-data="{field:'model', minWidth:120}">手机型号</th>
                <th  lay-data="{field:'resolution', minWidth:120}">分辨率</th>
                <th  lay-data="{field:'provider', minWidth:120}">运营商</th>
                <th  lay-data="{field:'system_version', minWidth:120}">系统版本</th>
                <th  lay-data="{field:'system', minWidth:100}">系统</th>
                <th  lay-data="{field:'network', minWidth:140}">网络</th>
                <th  lay-data="{field:'language', minWidth:100}">语言</th>
                <th  lay-data="{field:'ip', minWidth:160}">IP</th>
                <th  lay-data="{field:'created_at', minWidth:160}">时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $l)
                <tr>
                    <td>{{$l->package_name}}</td>
                    <td>{{$l->install_referrer}}</td>
                    <td>{{$l->referrer_click_timestamp_seconds}}</td>
                    <td>{{date('Y-m-d H:i:s',$l->install_begin_timestamp_seconds)}}</td>
                    <td>{{$l->referrer_click_timestamp_server_seconds}}</td>
                    <td>{{date('Y-m-d H:i:s',$l->install_begin_timestamp_server_seconds)}}</td>
                    <td>{{$l->install_version}}</td>
                    <td>{{$l->google_play_instant}}</td>
                    <td>{{$l->app_version}}</td>
                    <td>{{$l->device_id}}</td>
                    <td>{{$l->brand}}</td>
                    <td>{{$l->model}}</td>
                    <td>{{$l->resolution}}</td>
                    <td>{{$l->provider}}</td>
                    <td>{{$l->system_version}}</td>
                    <td>{{$l->system}}</td>
                    <td>{{$l->network}}</td>
                    <td>{{$l->language}}</td>
                    <td>{{$l->ip}}</td>
                    <td>{{$l->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $list->links('vendor.pagination.default') }}
        @else
            {{ $list->appends($appends)->links('vendor.pagination.default') }}
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
        }).use(['common' , 'table' , 'layer'], function () {
            var table = layui.table;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        });
    </script>
@endsection
