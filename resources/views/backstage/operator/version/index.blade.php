@extends('layouts.dashboard')
@section('layui-content')
        <div class="layui-tab" lay-filter="tab">
            <ul class="layui-tab-title">
                <li class="layui-this"  lay-id="0">Versions</li>
                <li  lay-id="1">Charts</li>

            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-fluid">
                        <form class="layui-form" action="" lay-filter="keep">
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder="yyyy-MM-dd - yyyy-MM-dd" @if(!empty($params['dateTime']))value="{{$params['dateTime']}}"@endif>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                                </div>
                            </div>
                        </form>
                        <table class="layui-table">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Version</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $l)
                                <tr>
                                    <td>{{$l->num}}</td>
                                    <td>{{$l->version}}</td>
                                    <td>{{$l->date}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="layui-tab-item"  id="layui-echarts">
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
        }).use(['element','common', 'laydate' , 'echarts'], function () {
            let $ = layui.jquery,
                element = layui.element,
                echarts = layui.echarts,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });

            let dom = document.getElementById("container");
            let myChart = echarts.init(dom);

            let option = {
                color: ['#80FFA5', '#00DDFF', '#37A2FF', '#FF0087', '#FFBF00'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },
                legend: {
                    data: @json($version)
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: @json($dates)
                    }
                ],
                yAxis: [{type: 'value'}],
                series: @json($line)
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
                window.onresize = function () {//用于使chart自适应高度和宽度
                    autoContainer();//重置容器高宽
                    myChart.resize();
                };
            }
            let autoContainer = function () {
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                myChart.resize();
            };
            element.on('collapse(tab)', function(data){
            });

            element.on('tab(tab)', function(data){
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
