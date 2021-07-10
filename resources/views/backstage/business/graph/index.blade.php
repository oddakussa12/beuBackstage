@extends('layouts.dashboard')
@section('layui-content')

    <div class="layui-fluid">
        <div id="layui-echarts" style="height: 95vh;min-height: 400px;">
            @foreach($result as $key=>$value)
                <div id="{{$key}}" style="width: 50%;min-height: 400px; height: 100%; float: left;"></div>
            @endforeach
        </div>
    </div>
@endsection
@section('footerScripts')
    @parent

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
            echarts: 'lay/modules/echarts',
        }).use(['element', 'echarts'], function () {
            let $ = layui.jquery, echarts = layui.echarts;

            @foreach($result as $key=>$value)
                let {{$key}} = echarts.init(document.getElementById("{{$key}}"));
                {{$key}}.setOption(@json($value))
            @endforeach
            let resize = function () {
                @foreach($result as $key=>$value)
                        {{$key}}.resize();
                @endforeach
            }
            window.onresize = function () {//用于使chart自适应高度和宽度
                autoContainer();//重置容器高宽
                resize();
            };
            let autoContainer = function () {
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                resize();
            };
            autoContainer();
        })
    </script>
@endsection
