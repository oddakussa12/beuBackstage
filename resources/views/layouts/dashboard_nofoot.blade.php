@extends('layouts.app')

<!-- 标题 -->
@section('title', trans('common.header.title'))

@section('content')
    <div class="layui-header">
        <!-- 引入头部 -->
        @include('layouts.nav')
    </div>
{{--    <div class="layui-side layui-bg-black">--}}
{{--        <div class="layui-side-scroll"  id="navBarId">--}}
{{--            <!-- 引入侧边栏 -->--}}
{{--            @include('layouts.side')--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree">
                @include('layouts.side')
            </ul>
        </div>
    </div>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div class="layui-fluid">
            @include('layouts.bread_crumb')
            @yield('layui-content')
        </div>

    </div>
@endsection

@section('footerScripts')
    @parent
    <script>
        layui.use(['element'], function(){
            var element = layui.element;
        });
    </script>
@endsection