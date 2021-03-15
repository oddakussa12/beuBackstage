@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-header">
        <!-- 引入头部 -->
        @include('layouts.nav')
    </div>
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
            @include('layouts.bread_crumb')

        <div class="layui-tab" lay-filter="dnu">
            <ul class="layui-tab-title">
                <li lay-id="0">DNU</li>
                <li class="layui-this" lay-id="1">DNU</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item ">
                    <div class="layui-fluid">
                        <form class="layui-form" action="" lay-filter="keep">
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                                <div class="layui-inline">
                                    <select name="country_code" lay-verify="" lay-search>
                                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                                        <option value="all" @if($country_code=='all') selected @endif>All</option>
                                        @foreach($counties  as $country)
                                            <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="start" id="start" readonly="" placeholder="yyyy-MM-dd" value="{{$startTime}}">
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                                </div>
                            </div>
                        </form>
                        <table class="layui-table" >
                            <thead>
                            <tr>
                                <th>Day</th>
                                <th>DNU</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $l)
                                <tr>
                                    <td>{{$l['date']}}</td>
                                    <td>{{$l['num']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="layui-tab-item layui-show"  id="layui-echarts">
                    <div id="container" style="height: 100%">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            echarts: 'lay/modules/echarts',
        }).use(['element','common' , 'table', 'laydate' , 'echarts'], function () {
            var $ = layui.jquery,
                element = layui.element,
                table = layui.table,
                common = layui.common,
                echarts = layui.echarts,
                laydate = layui.laydate;
            laydate.render({
                elem: '#start'
                ,range: false
                ,max : -2
                ,lang: 'en'
            });

            var dom = document.getElementById("container");
            var myChart = echarts.init(dom);
            var app = {};

            var option;



            option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['dnu']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: @json($xAxis)
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: 'dnu',
                        type: 'line',
                        data: @json($dnu)
                    }
                ]
            };


            if (option && typeof option === 'object') {
                myChart.setOption(option);
                window.onresize = function () {//用于使chart自适应高度和宽度
                    autoContainer();//重置容器高宽
                    myChart.resize();
                };
            }
            var autoContainer = function () {
                //container.clientWidth和container.clientHeight //自适应容器宽和高
                // window.innerWidth和window.innerHeight//自适应浏览器宽和高
                // myChart.style.height = $(".layui-body").clientHeight + 'px';
                // $('#layui-echarts').style.height =  $(".layui-body").clientHeight + 'px';
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                // console.log($('#layui-echarts').clientHeight);
                myChart.resize();
                //cityChart.style.height = $(".layui-col-sm12").clientHeight + 'px';
            };

            element.on('tab(dnu)', function(data){
                if(data.index==1)
                {
                    autoContainer();
                }
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
