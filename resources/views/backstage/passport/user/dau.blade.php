@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-fluid">
        <div class="layui-tab" lay-filter="dau">
            <ul class="layui-tab-title">
                <li class="layui-this"  lay-id="0">DAU</li>
                <li  lay-id="1">DAU</li>

            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form class="layui-form" action="" lay-filter="keep">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                                <div class="layui-input-inline">
                                    <select name="country_code" lay-verify="" lay-search>
                                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                                        <option value="all" @if($country_code=='all') selected @endif>All</option>
                                        @foreach($countries  as $country)
                                            <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="period" id="period" readonly="" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$period}}">
                                </div>
                                <div class="layui-input-inline">
                                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="layui-table"  class="layui-table"  lay-filter="common_table">
                        <thead>
                        <tr>
                            <th lay-data="{field:'Day', width:120 ,fixed: 'left'}">Day</th>
                            <th lay-data="{field:'DAU', minWidth:100}">DAU</th>
                            <th lay-data="{field:'0NUM', width:100}">0NUM</th>
                            <th lay-data="{field:'0NUM%', width:100}">0NUM%</th>
                            <th lay-data="{field:'1NUM', width:100}">1NUM</th>
                            <th lay-data="{field:'1NUM%', width:100}">1NUM%</th>
                            <th lay-data="{field:'2NUM', width:100}">2NUM</th>
                            <th lay-data="{field:'2NUM%', width:100}">2NUM%</th>
                            <th lay-data="{field:'3NUM', width:100}">>3NUM</th>
                            <th lay-data="{field:'3NUM%', width:100}">>3NUM%</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $l)
                            <tr>
                                <td>{{$l['date']}}</td>
                                <td>{{$l['dau']}}</td>
                                <td>{{$l['zero']}}</td>
                                @if ($l['dau']==0)
                                    <td>0%</td>
                                @else
                                    <td>{{round($l['zero']/$l['dau']*100 , 2)}}%</td>
                                @endif

                                <td>{{$l['one']}}</td>
                                @if ($l['dau']==0)
                                    <td>0%</td>
                                @else
                                    <td>{{round($l['one']/$l['dau']*100 , 2)}}%</td>
                                @endif

                                <td>{{$l['two']}}</td>
                                @if ($l['dau']==0)
                                    <td>0%</td>
                                @else
                                    <td>{{round($l['two']/$l['dau']*100 , 2)}}%</td>
                                @endif

                                <td>{{$l['gt3']}}</td>
                                @if ($l['dau']==0)
                                    <td>0%</td>
                                @else
                                    <td>{{round($l['gt3']/$l['dau']*100 , 2)}}%</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="layui-tab-item"  id="layui-echarts">
                    <div id="container" style="height: 100%">
                </div>
            </div>
        </div>
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
                elem: '#period'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });


            table.init('common_table', {
                page:false,
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
                    data: ['dau', 'zero', 'one', 'two', 'gt3']
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
                        name: 'dau',
                        type: 'line',
                        data: @json($dau)
                    },
                    {
                        name: 'zero',
                        type: 'line',
                        data: @json($zero)
                    },
                    {
                        name: 'one',
                        type: 'line',
                        data: @json($one)
                    },
                    {
                        name: 'two',
                        type: 'line',
                        data: @json($two)
                    },
                    {
                        name: 'gt3',
                        type: 'line',
                        data: @json($gt3)
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
            element.on('tab(dau)', function(data){
                if(data.index==1)
                {
                    autoContainer();
                }
            });
            var str = [
                {
                    "国家":"东帝汶",
                    "时区":"Asia/Dili",
                    "当地时间":"{{$utc}}",
                    "格林威治时间":"{{$Dili}}",
                    "北京时间":"{{$DiliBj}}",
                },
                {
                    "国家":"格林纳达",
                    "时区":"America/Grenada",
                    "当地时间":"{{$utc}}",
                    "格林威治时间":"{{$Grenada}}",
                    "北京时间":"{{$GrenadaBj}}",
                },
                {
                    "国家":"毛里求斯",
                    "时区":"Indian/Mauritius",
                    "当地时间":"{{$utc}}",
                    "格林威治时间":"{{$Mauritius}}",
                    "北京时间":"{{$MauritiusBj}}",
                }
            ];
            console.table(str);
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
