@extends('layouts.dashboard')
@section('layui-content')
        <div class="layui-tab" lay-filter="dnu">
            <ul class="layui-tab-title">
                <li lay-id="0">DNU</li>
                <li class="layui-this" lay-id="1">DNU</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item ">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">Type:</label>
                                <div class="layui-input-inline">
                                    <select name="type"  lay-filter="type" lay-search>
                                        <option value="country" @if($type=='country') selected @endif>Country</option>
                                        <option value="school" @if($type=='school') selected @endif>School</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-inline"  @if($type!='school') style="display: none;"  @endif id="schoolDiv">
                                <label class="layui-form-label">School:</label>
                                <div class="layui-input-inline">
                                    <select name="school" lay-verify="" lay-search id="school">
                                        <option value="">{{trans('user.form.placeholder.user_school')}}</option>
                                        @foreach($schools  as $s)
                                            <option value="{{$s}}" @if($s==$school) selected @endif>{{$s}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if(Auth::user()->hasRole('administrator'))
                            <div class="layui-inline"  @if($type!='country') style="display: none;"  @endif id="countryDiv">
                                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                                <div class="layui-input-inline">
                                    <select name="country_code" lay-verify="" lay-search id="country">
                                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                                        <option value="all" @if($country_code=='all') selected @endif>All</option>
                                        @foreach($countries  as $country)
                                            <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="start" id="start" readonly="" placeholder="yyyy-MM-dd" value="{{$startTime}}">
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
                            <th lay-data="{field:'DNU', minWidth:100}">DNU</th>
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
                <div class="layui-tab-item layui-show"  id="layui-echarts">
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
                form = layui.form,
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
                        data: @json($dnu),
                        markPoint: {
                            data: [
                                {type: 'max', name: 'MAX'},
                                {type: 'min', name: 'MIN'}
                            ]
                        },
                        markLine: {
                            data: [
                                {
                                    type: "average"
                                }
                            ]
                        },
                        itemStyle : { normal: {label : {show: true}}}
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
            autoContainer();
            form.render();
            form.on('select(type)', function (data) {
                if (data.value == 'country') {
                    $("#schoolDiv").hide();
                    $("#school").attr("disabled", "true");
                    $("#school").removeAttr("lay-verify");

                    $("#countryDiv").show();
                    $("#country").removeAttr("disabled");
                    $("#country").attr("lay-verify", "required");
                }else{
                    $("#countryDiv").hide();
                    $("#country").attr("disabled", "true");
                    $("#country").removeAttr("lay-verify");


                    $("#schoolDiv").show();
                    $("#school").removeAttr("disabled");
                    $("#school").attr("lay-verify", "required");
                }
                form.render('select');
                console.log(data);
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
