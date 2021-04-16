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
            <div class="layui-col-xs10 dev-title"></div>
            <div class="layui-col-md1">
                <div class="layui-panel left">
                    <div style="padding: 30px;"></div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="panel oper">
                    <div class="layui-panel">
                        <div class="layui-title"><h1>Operations</h1></div>
                        <div class="operBg">
                            <div class="layui-col-xs4 bg-left">45% <br />reach</div>
                            <div class="layui-col-xs4 bg-middle"><img src="{{url('plugin/layui/images/goal')}}/Group843.png"></div>
                            <div class="layui-col-xs4 bg-right">50K <br /> Target</div>
                        </div>
                            <div class="content">
                            </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="panel hr">
                    <div class="layui-panel">
                        <div class="layui-title"><h1>HR</h1></div>
                        <div class="operBg">
                            <div class="layui-col-xs4 bg-left">42% <br />reach</div>
                            <div class="layui-col-xs4 bg-middle"><img src="{{url('plugin/layui/images/goal')}}/Group844.png"></div>
                            <div class="layui-col-xs4 bg-right">78% <br /> Target</div>
                        </div>
                            <div class="content">
                            </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="panel prod">
                    <div class="layui-panel">
                        <div class="layui-title"><h1>Product</h1></div>
                        <div class="operBg">
                            <div class="layui-col-xs4 bg-left">45% <br />Target</div>
                            <div class="layui-col-xs4 bg-middle"><img src="{{url('plugin/layui/images/goal')}}/Group845.png"></div>
                            <div class="layui-col-xs4 bg-right">45% <br /> Target</div>
                        </div>
                        <div class="content">
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="panel">
                    <div class="layui-panel dev">
                        <div class="layui-title"><h1>Development</h1></div>
                        <div class="operBg">
                            <div class="layui-col-xs4 bg-left">40% <br />Target</div>
                            <div class="layui-col-xs4 bg-middle"><img src="{{url('plugin/layui/images/goal')}}/Group846.png"></div>
                            <div class="layui-col-xs4 bg-right">100% <br /> Target</div>
                        </div>
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
                <div class="panel data">
                    <div class="layui-panel">
                        <div class="layui-title"><h1>Data</h1></div>
                        <div class="operBg">
                            <div class="layui-col-xs4 bg-left">40% <br />Target</div>
                            <div class="layui-col-xs4 bg-middle"><img src="{{url('plugin/layui/images/goal')}}/Group847.png"></div>
                            <div class="layui-col-xs4 bg-right">100% <br /> Target</div>
                        </div>
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
        .horizontal{
            margin-top: 60px;
            width: 80px;
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
            height: 65vh;
        }
        .panel{
            margin:30px 10px 0 0;
            border-radius: 10px;
        }
        .oper {
            color: #FFDA8F;
            box-shadow: 0 0 4px 2px #FFDA8F;
            border: #FFDA8F 3px solid;
        }
        .hr {
            box-shadow: 0 0 4px 2px #FF8FFF;
            border: #FF8FFF 3px solid;
        }
        .data {
            box-shadow: 0 0 4px 2px #8FDAFF;
            border: #8FDAFF 3px solid;
        }
        .dev {
            box-shadow: 0 0 4px 2px #B4FF8F;
            border: #B4FF8F 3px solid;
        }
        .prod {
            box-shadow: 0 0 4px 2px #FF8F8F;
            border: #FF8F8F 3px solid;
        }
        .layui-panel{
            border-radius: 10px;
        }
        .dev-title{
            text-align: center;
            background: url("{{url('plugin/layui/images/goal')}}/path.png") no-repeat 15px center;
            margin: 0 8.33%;
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
            font-size: small;
        }
        .content{
            padding: 5px;
        }
        .layui-slider-vertical{
            height: 500px;
            width: 80px;
            margin: 0px;
        }

        input[type='checkbox']{
            width: 15px;
            height: 15px;
        }
        ul li{
            padding-bottom: 10px;
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
        .layui-col-space1 {height: 70vh}
        .operBg {
            vertical-align: middle;
            width: 100%;
            height: 33%;
        }
        .bg-left, .bg-middle, .bg-right {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .bg-left {
        }
        .bg-middle {
        }
        .bg-right {
        }
        .layui-title {
            text-align: center;
            padding-top: 10px;
        }
        .operBg img {
            width: 100%;
        }

        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
