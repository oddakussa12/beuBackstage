<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{{ trans('common.header.title') }} | @yield('title', trans('common.backend_home'))</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="renderer" content="webkit">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <link rel="stylesheet" href="{{ asset('plugin/layui/css/layui.css') }}" media="all">
        <link rel="stylesheet" href="{{ asset('plugin/layui/css/layui.mobile.css') }}" media="all">
    </head>


    <body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <!-- 后台页面内容 -->
        @yield('content')
    </div>
    @section('footerScripts')
        <!-- 引入layui插件 -->
        <script src="{{ asset('plugin/layui/layui.all.js') }}"></script>
        <script src="{{ asset('plugin/layui/lay/modules/global.js') }}"></script>
    @show
    </body>
</html>