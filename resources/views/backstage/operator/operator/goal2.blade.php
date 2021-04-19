@extends('layouts.app')
@section('content')
    <div  class="layui-fluid">
        <div class="layui-row">
            <div class="layui-col-md3"><div style="padding: 30px;"></div></div>
            <div class="layui-col-md6 title">XMT from 2021</div>
            <div class="layui-col-md3"><div style="padding: 30px;"></div></div>
        </div>
        <div class="layui-row">
            <div class="layui-progress">
                <div class="layui-progress-bar" lay-percent="{{$dauData['percentage']}}"><span style="padding-left: 40px;">{{$dauData['current']}}</span></div>
                <img src="{{url('plugin/layui/images/goal')}}/1@10x.png" id="vertical">
                <span class="dau">16k</span>
            </div>
        </div>
        <div class="layui-row bottom layui-col-space1">
            <div class="layui-col-md1">
                <div class="layui-panel left">
                    <div style="padding: 30px;"></div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/dev1@10x.png" height="25px">
                </div>
                <div class="panel">
                    <div class="layui-panel dev">
                        <div class="content">
                            <ul class="site-doc-bgcolor">
                                <li>
                                    <label><input name="dev[]" type="checkbox" value="1" /> 对于不同前、后端接口的功能上增加“时效性缓存系统”(针对不同的接口,缓存时长各有不同、可以统一封装一套缓存系统)能有效提升用户体验，减少服务器并发访问压力等</label>
                                </li>
                                <li>
                                    <label><input name="dev[]" type="checkbox" value="2" /> 对于一些"一定会成功、网络缺不太好、或后端响应时间较长"的接口，做一个入库操作，找一个准确的时机、做合适的轮训操作能很大幅度提升用户体验
                                    <br />时间周期:4月底之前优化</label>
                                </li>
                                <li>
                                    <label><input name="dev[]" type="checkbox" value="3" /> 整理规范GitHub</label>
                                </li>
                                <li>
                                    <label><input name="dev[]" type="checkbox" value="4" checked="checked" /> 启用trello</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/2@10x.png" height="25px">
                </div>
                <div class="no-panel">
                    <div class="layui-panel oper">
                        <div class="progress">
                            <div class="slideoper">
                                <span class="goal">60K</span>
                                <div class="layui-slider layui-slider-vertical oper">
                                    <div class="layui-slider-wrap" style="bottom: 66.66667%;">
                                        <span class="current">{{$operData['middle']}}</span>
                                    </div>
                                    <div class="layui-slider-wrap" style="bottom: {{$operData['percentage']}}">
                                        <span class="current">{{$operData['current']}}</span>
                                    </div>
                                    <img src="{{url('plugin/layui/images/goal')}}/1510x.png" class="horizontal horizontal-oper">
                                    <div class="layui-slider-bar layui-slider-bar-oper"></div>
                                </div>
                                <span class="mark">拉新</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/3@10x.png" height="25px">
                </div>
                <div class="no-panel">
                    <div class="layui-panel hr">
                        <div class="progress">
                            <div class="slideoper">
                                <span class="goal">50</span>
                                <div class="layui-slider layui-slider-vertical hr">
                                    <div class="layui-slider-wrap" style="bottom: 72%;">
                                        <span class="current">36</span>
                                    </div>
                                    <div class="layui-slider-wrap" style="bottom: {{$hrData['percentage']}}">
                                        <span class="current">{{$hrData['current']}}</span>
                                    </div>
                                    <img src="{{url('plugin/layui/images/goal')}}/1510x.png" class="horizontal horizontal-hr">
                                    <div class="layui-slider-bar layui-slider-bar-hr"></div>
                                </div>
                                <span class="mark">面试</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/4@10x.png" height="25px">
                </div>
                <div class="panel">
                    <div class="layui-panel data">
                        <div class="content">
                            <ul class="site-doc-bgcolor">
                                <li>
                                    <label><input name="data[]" type="checkbox" value="1" /> 协助产品部门进行留存率的提升，提供更详细的留存数据（如聊天用户留存、添加三个好友用户留存等）</label>
                                </li>
                                <li>
                                    <label><input name="data[]" type="checkbox" value="2" /> 协助运营部门进行拉新推广，提供推广活动数据，用户聊天数据，新用户行为数据等 </label>
                                </li>
                                <li>
                                    <label><input name="data[]" type="checkbox" value="3" /> 数据会议由全局数据展示与专题分析交叉进行 </label>
                                </li>
                                <li>
                                    <label><input name="data[]" type="checkbox" value="4" /> 与马特商量用户时长的数据统计需求，并落实数据统计 </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/5@10x.png" height="25px">
                </div>
                <div class="no-panel">
                    <div class="layui-panel prod">
                        <div class="progress">
                            <div class="slideoper">
                                <span class="goal">30%</span>
                                <div class="layui-slider layui-slider-vertical prod-retention">
                                    <div class="layui-slider-wrap" style="bottom: 63.33%;">
                                        <span class="current">19%</span>
                                    </div>
                                    <div class="layui-slider-wrap" style="bottom: {{$prodRetentionData['percentage']}};">
                                        <span class="current">{{$prodRetentionData['current']}}</span>
                                    </div>
                                    <img src="{{url('plugin/layui/images/goal')}}/1510x.png" class="horizontal horizontal-prod-retention">
                                    <div class="layui-slider-bar layui-slider-bar-prod-retention"></div>
                                </div>
                                <span class="mark">留存</span>
                            </div>
                            <div class="slideoper">
                                <span class="goal">80</span>
                                <div class="layui-slider layui-slider-vertical prod-mask">
                                    <div class="layui-slider-wrap" style="bottom: 62.5%;">
                                        <span class="current">50</span>
                                    </div>
                                    <div class="layui-slider-wrap" style="bottom: {{$prodMaskData['percentage']}};">
                                        <span class="current">{{$prodMaskData['current']}}</span>
                                    </div>
                                    <img src="{{url('plugin/layui/images/goal')}}/1510x.png" class="horizontal horizontal-prod-mask">
                                    <div class="layui-slider-bar layui-slider-bar-prod-mask"></div>
                                </div>
                                <span class="mark">面具</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md1">
                <div class="layui-panel right">
                    <div style="padding: 30px;"></div>
                </div>
            </div>
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
            echarts: 'lay/modules/echarts',
        }).use(['common' , 'layer' , 'element' , 'echarts', 'slider'], function () {
            var $ = layui.jquery,
                common = layui.common,
                layer = layui.layer,
                slider = layui.slider,
                echarts = layui.echarts,
                element = layui.element;
                setTimeout(function() {
                    window.location.reload();
                }, 60000);
            {{--let dom = document.getElementById("container");--}}
            {{--let myChart = echarts.init(dom);--}}

            {{--let option = {--}}
            {{--    tooltip: {--}}
            {{--        trigger: 'axis',--}}
            {{--    },--}}
            {{--    xAxis: {--}}
            {{--        type: 'category',--}}
            {{--        boundaryGap: false,--}}
            {{--        data: @json($dates)--}}
            {{--    },--}}
            {{--    yAxis: {--}}
            {{--        type: 'value'--}}
            {{--    },--}}
            {{--    series: @json($line)--}}
            {{--};--}}

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
        })
    </script>
    <style>
        #vertical{
            height: 100px;
            margin-left: 94.11%;
        }
        .current{
            margin: 0 auto;
            color: white;
            font-weight: bold;
            font-size: large;
            bottom: 0;
        }
        .dau{
            color: white;
            font-weight: bold;
            font-size: large;
        }
        .dau-goal{
            color: white;
            font-weight: bold;
            font-size: large;
        }
        .horizontal{
            margin-top: 60px;
            width: 80px;
        }
        .horizontal-oper{
            margin-top: {{$operData['marginTop']}};
        }
        .horizontal-hr{
            margin-top: {{$hrData['marginTop']}};
        }
        .horizontal-prod-retention{
            margin-top: {{$prodRetentionData['marginTop']}};
        }
        .horizontal-prod-mask{
            margin-top: {{$prodMaskData['marginTop']}};
        }
        .layui-progress{
            height:100px;
            position: relative;
            width: 60%;
            margin: 0 auto;
            background-image: url("{{url('plugin/layui/images/goal')}}/210x.png");
            background-repeat:no-repeat;
            background-size:100% 100%;
            -moz-background-size:100% 100%;
        }
        .layui-progress-bar{
            color: white;
            font-weight: bold;
            font-size: large;
            line-height: 100px;
            height: 100px;
            background-color:rgba(255,255,255,0.4);
        }
        .bottom{
            margin-top: 50px;
            border-radius: 1px;
        }
        .layui-panel {
            /*background-color: green;*/
        }
        .panel{
            margin-top:30px;
            box-shadow: 0px 0px 4px 0px #909090;
            border-radius: 10px;
        }
        .no-panel{
            margin-top:30px;
            border-radius: 10px;
        }
        .layui-panel{
            border-radius: 10px;
        }
        .dev-title{
            text-align: center;
            background: url("{{url('plugin/layui/images/goal')}}/path.png") no-repeat 15px center;
        }
        .dev-title img{
            height: 36px;
            margin:0 auto;
        }
        .layui-table-cell {
            height: 60px;
            line-height: 60px;
        }
        ul li label{
            padding-left:40px;
            font-size: large;
        }
        .content{
            padding: 5px;
        }
        .layui-slider-vertical{
            height: 500px;
            width: 80px;
            margin: 0px;
        }
        .slideoper .oper{
            background-color: rgba(233, 128, 0, 1)
        }
        .slideoper .hr{
            background-color: rgba(0, 175, 233, 1)
        }
        .slideoper .prod-retention{
            background-color: rgba(0, 233, 221, 1)
        }
        .slideoper .prod-mask{
            background-color: rgba(0, 233, 221, 1)
        }
        .layui-slider-vertical .layui-slider-bar{
            width: 80px;
            background-color:rgba(255,255,255,0.4);
            bottom: 0%;
        }
        .layui-slider-vertical .layui-slider-bar-oper{
            height: {{$operData['percentage']}};
        }
        .layui-slider-vertical .layui-slider-bar-hr{
            height: {{$hrData['percentage']}};
        }
        .layui-slider-vertical .layui-slider-bar-prod-retention{
            height: {{$prodRetentionData['percentage']}};
        }
        .layui-slider-vertical .layui-slider-bar-prod-mask{
            height: {{$prodMaskData['percentage']}};
        }
        .layui-slider-vertical .layui-slider-wrap{
            left: 24px;
            margin-bottom: 5px;
        }
        .progress{
            text-align: center;
        }
        input[type='checkbox']{
            width: 15px;
            height: 15px;
        }
        ul li{
            padding-bottom: 10px;
        }
        .slideoper{
            display: inline-block;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .mark{
            font-weight: bold;
            font-size: large;
        }
        .title{
            padding-top: 20px;
            text-align: center;
            line-height: normal;
            font-weight: bold;
            font-size: xx-large;
        }
        .goal{
            font-weight: bold;
            font-size: large;
        }
        .arrow{
            display: inline-block;
            background: url("{{url('plugin/layui/images/goal')}}/410x.png") no-repeat 0px center;
            height: 64px;
            width: 120px;
            color: white;
            font-weight: bold;
            font-size: x-large;
            padding-top: 15px;
        }
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
