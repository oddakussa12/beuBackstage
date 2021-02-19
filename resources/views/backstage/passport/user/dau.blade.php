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
        <div class="layui-collapse" lay-filter="dau">
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">DAU</h2>
                <div class="layui-colla-content layui-show">
                    <div class="layui-fluid">
                        <form class="layui-form" action="" lay-filter="keep">
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                                <div class="layui-inline">
                                    <select name="country_code" lay-verify="" lay-search>
                                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                                        @foreach($counties  as $country)
                                            <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="period" id="period" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$period}}">
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
                                <th>DUA</th>
                                <th>0NUM</th>
                                <th>0NUM%</th>
                                <th>1NUM</th>
                                <th>1NUM%</th>
                                <th>2NUM</th>
                                <th>2NUM%</th>
                                <th>>3NUM</th>
                                <th>>3NUM%</th>
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
                </div>
            </div>
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">DAU</h2>
                <div class="layui-colla-content layui-show " id="layui-echarts"><div id="container" style="height: 100%"></div></div>
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
                elem: '#period'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });



            table.init('user_table', { //转化静态表格
                page:false
            });

            var dom = document.getElementById("container");
            var myChart = echarts.init(dom);
            var app = {};

            var option;



            option = {
                title: {
                    text: '折线图堆叠'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['邮件营销', '联盟广告', '视频广告', '直接访问', '搜索引擎']
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
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: '邮件营销',
                        type: 'line',
                        stack: '总量',
                        data: [120, 132, 101, 134, 90, 230, 210]
                    },
                    {
                        name: '联盟广告',
                        type: 'line',
                        stack: '总量',
                        data: [220, 182, 191, 234, 290, 330, 310]
                    },
                    {
                        name: '视频广告',
                        type: 'line',
                        stack: '总量',
                        data: [150, 232, 201, 154, 190, 330, 410]
                    },
                    {
                        name: '直接访问',
                        type: 'line',
                        stack: '总量',
                        data: [320, 332, 301, 334, 390, 330, 320]
                    },
                    {
                        name: '搜索引擎',
                        type: 'line',
                        stack: '总量',
                        data: [820, 932, 901, 934, 1290, 1330, 1320]
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
                $('#layui-echarts').style.height =  $(".layui-body").clientHeight + 'px';
                console.log($('#layui-echarts').clientHeight);
                myChart.resize();
                //cityChart.style.height = $(".layui-col-sm12").clientHeight + 'px';
            };
            element.on('collapse(dau)', function(data){
                console.log(data.show); //得到当前面板的展开状态，true或者false
                console.log(data.title); //得到当前点击面板的标题区域DOM对象
                console.log(data.content); //得到当前点击面板的内容区域DOM对象
                if(data.show)
                {
                    autoContainer();//重置容器高宽
                }
            });



        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
