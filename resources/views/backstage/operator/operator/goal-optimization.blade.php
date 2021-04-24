@extends('layouts.app')
@section('content')
    <div  class="layui-fluid">
        <div class="layui-row">
            <div class="layui-col-md1"><div style="padding: 30px;"></div></div>
            <div class="layui-col-md10 title" id="dau"></div>
            <div class="layui-col-md1"><div style="padding: 30px;"></div></div>
        </div>
        <div class="layui-row bottom layui-col-space10">
            <div class="layui-col-md1">
                <div class="layui-panel left">
                    <div style="padding: 30px;"></div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="div-title">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-operate">
                    <div class="layui-row layui-item-title">
                        <h2>Operations</h2>
                    </div>
                    <span class="operate-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$operData['current']}}<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$operData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <span class="target-content"></span>
                    <svg class="editorial editorial-operate"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         viewBox="0 24 150 28"
                         preserveAspectRatio="none">
                        <defs>
                            <path id="gentle-wave-operate"
                                  d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                        </defs>
                        <g class="parallax">
                            <use xlink:href="#gentle-wave-operate" x="50" y="0" fill="#FFD24A"/>
                            <use xlink:href="#gentle-wave-operate" x="50" y="1" fill="#FFEABF"/>
                            <use xlink:href="#gentle-wave-operate" x="50" y="2" fill="#FFDA8F"/>
                        </g>
                    </svg>
                    <div class="content content-operate">
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="div-title">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-hr">
                    <div class="layui-row layui-item-title">
                        <h2>HR</h2>
                    </div>
                    <span class="hr-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$hrData['current']}}<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$hrData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <svg class="editorial-hr"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         viewBox="0 24 150 28"
                         preserveAspectRatio="none">
                        <defs>
                            <path id="gentle-wave-hr"
                                  d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                        </defs>
                        <g class="parallax">
                            <use xlink:href="#gentle-wave-hr" x="50" y="0" fill="#FFAFFF"/>
                            <use xlink:href="#gentle-wave-hr" x="50" y="1" fill="#FFBFFF"/>
                            <use xlink:href="#gentle-wave-hr" x="50" y="2" fill="#FF8FFF"/>
                        </g>
                    </svg>
                    <div class="content content-hr">
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title-middle">
                    <div class="wave-num"><b id="num">{{$dauData['percentage']}}</b></div>
                    <div class="wave-n">
                        <div class="wave" style="height: {{$dauData['percentage']}};">&nbsp;</div>
                    </div>
                </div>
                <div class="layui-panel layui-panel-middle">
                    <div class="layui-panel layui-slider layui-middle layui-panel-retention">
                        <span class="retention-icon layui-row layui-icon-div-middle">
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">{{$prodRetentionData['current']}}<br />Reach</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">Retention</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">{{$prodRetentionData['goal']}}<br />Target</h2>
                            </div>
                        </span>
                        <svg class="editorial-retention"
                             xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink"
                             viewBox="0 24 150 28"
                             preserveAspectRatio="none">
                            <defs>
                                <path id="gentle-wave-prod"
                                      d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                            </defs>
                            <g class="parallax">
                                <use xlink:href="#gentle-wave-prod" x="50" y="0" fill="#FFBFBF"/>
                                <use xlink:href="#gentle-wave-prod" x="50" y="1" fill="#FFBFBF"/>
                                <use xlink:href="#gentle-wave-prod" x="50" y="2" fill="#FF8F8F"/>
                            </g>
                        </svg>
                        <div class="content content-retention">
                            <span></span>
                        </div>
                    </div>
                    <div class="layui-panel layui-slider layui-middle layui-panel-mask">
                        <span class="mask-icon layui-row layui-icon-div-middle">
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">{{$prodMaskData['current']}}<br />Reach</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">Masks</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">{{$prodMaskData['goal']}}<br />Target</h2>
                            </div>
                        </span>
                        <svg class="editorial-mask"
                             xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink"
                             viewBox="0 24 150 28"
                             preserveAspectRatio="none">
                            <defs>
                                <path id="gentle-wave-mask"
                                      d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                            </defs>
                            <g class="parallax">
                                <use xlink:href="#gentle-wave-mask" x="50" y="0" fill="#FFBFBF"/>
                                <use xlink:href="#gentle-wave-mask" x="50" y="1" fill="#FFBFBF"/>
                                <use xlink:href="#gentle-wave-mask" x="50" y="2" fill="#FF8F8F"/>
                            </g>
                        </svg>
                        <div class="content content-mask">
                            <span></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="layui-col-md2">
                <div class="div-title">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-dev">
                    <div class="layui-row layui-item-title">
                        <h2>Development</h2>
                    </div>
                    <span class="dev-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$devData['current']}}<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$devData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <span class="target-content">
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
                    </span>
                    <svg class="editorial-dev"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         viewBox="0 24 150 28"
                         preserveAspectRatio="none">
                        <defs>
                            <path id="gentle-wave-dev"
                                  d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                        </defs>
                        <g class="parallax">
                            <use xlink:href="#gentle-wave-dev" x="50" y="0" fill="#55FF00"/>
                            <use xlink:href="#gentle-wave-dev" x="50" y="1" fill="#D4FFBF"/>
                            <use xlink:href="#gentle-wave-dev" x="50" y="2" fill="#B4FF8F"/>
                        </g>
                    </svg>
                    <div class="content content-dev">
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="div-title">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-data">
                    <div class="layui-row layui-item-title">
                        <h2>Data</h2>
                    </div>
                    <span class="data-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$dataData['current']}}<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$dataData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <span class="target-content">
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
                    </span>
                    <svg class="editorial-data"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         viewBox="0 24 150 28"
                         preserveAspectRatio="none">
                        <defs>
                            <path id="gentle-wave-data"
                                  d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z" />
                        </defs>
                        <g class="parallax">
                            <use xlink:href="#gentle-wave-data" x="50" y="0" fill="#BFFAFF"/>
                            <use xlink:href="#gentle-wave-data" x="50" y="1" fill="#BFEAFF"/>
                            <use xlink:href="#gentle-wave-data" x="50" y="2" fill="#8FDAFF"/>
                        </g>
                    </svg>
                    <div class="content content-data">
                        <span></span>
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
    <script type="text/javascript" src="{{url('plugin/layui/lay/modules')}}/echarts.min.js"></script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element'], function () {
            var $ = layui.jquery;
            var chart_dau = echarts.init(
                document.getElementById('dau'), 'light', {renderer: 'canvas'});
            var option = {
                "animation": true,
                "animationThreshold": 2000,
                "animationDuration": 1000,
                "animationEasing": "cubicOut",
                "animationDelay": 0,
                "animationDurationUpdate": 300,
                "animationEasingUpdate": "cubicOut",
                "animationDelayUpdate": 0,
                "series": [
                    {
                        "type": "line",
                        "name": "Real DAU",
                        "connectNulls": false,
                        "symbolSize": 4,
                        "showSymbol": true,
                        "smooth": false,
                        "clip": true,
                        "step": false,
                        "data": @json($dauList),
                        "hoverAnimation": true,
                        "label": {
                            "show": true,
                            "position": "top",
                            "color": "black",
                            "margin": 8,
                            "fontSize": 16
                        },
                        "lineStyle": {
                            "show": true,
                            "width": 5,
                            "opacity": 1,
                            "curveness": 0,
                            "type": "solid",
                            "color": "red"
                        },
                        "areaStyle": {
                            "opacity": 0
                        },
                        "markPoint": {
                            "label": {
                                "show": true,
                                "position": "inside",
                                "color": "#fff",
                                "margin": 8
                            },
                            "data": [
                                {
                                    "type": "max"
                                },
                                {
                                    "type": "min"
                                }
                            ]
                        },
                        "markLine": {
                            "silent": false,
                            "precision": 2,
                            "label": {
                                "show": true,
                                "position": "top",
                                "margin": 8
                            },
                            "data": [
                                {
                                    "type": "average"
                                }
                            ]
                        },
                        "zlevel": 0,
                        "z": 0
                    },
                    {
                        "type": "line",
                        "name": "Target DAU",
                        "connectNulls": false,
                        "symbolSize": 4,
                        "showSymbol": true,
                        "smooth": false,
                        "clip": true,
                        "step": false,
                        "data": @json($dauList),
                        "hoverAnimation": true,
                        "label": {
                            "show": true,
                            "position": "top",
                            "color": "black",
                            "margin": 8,
                            "fontSize": 16
                        },
                        "lineStyle": {
                            "show": true,
                            "width": 5,
                            "opacity": 1,
                            "curveness": 0,
                            "type": "solid",
                            "color": "black"
                        },
                        "areaStyle": {
                            "opacity": 0
                        },
                        "markLine": {
                            "silent": false,
                            "precision": 2,
                            "label": {
                                "show": true,
                                "position": "top",
                                "margin": 8
                            },
                            "data": [
                                {
                                    "type": "average"
                                }
                            ]
                        },
                        "zlevel": 0,
                        "z": 0
                    }
                ],
                "legend": [
                    {
                        "data": [
                            "Real DAU",
                            "Target DAU"
                        ],
                        "selected": {
                            "Real DAU": true,
                            "Target DAU": true
                        },
                        "show": true,
                        "padding": 5,
                        "itemGap": 10,
                        "itemWidth": 25,
                        "itemHeight": 14
                    }
                ],
                "tooltip": {
                    "show": true,
                    "trigger": "axis",
                    "triggerOn": "mousemove|click",
                    "axisPointer": {
                        "type": "line"
                    },
                    "showContent": true,
                    "alwaysShowContent": false,
                    "showDelay": 0,
                    "hideDelay": 100,
                    "textStyle": {
                        "fontSize": 14
                    },
                    "borderWidth": 0,
                    "padding": 5
                },
                "xAxis": [
                    {
                        "show": true,
                        "scale": false,
                        "nameLocation": "end",
                        "nameGap": 15,
                        "gridIndex": 0,
                        "inverse": false,
                        "offset": 0,
                        "splitNumber": 5,
                        "minInterval": 0,
                        "splitLine": {
                            "show": false,
                            "lineStyle": {
                                "show": true,
                                "width": 1,
                                "opacity": 1,
                                "curveness": 0,
                                "type": "solid"
                            }
                        },
                        "data": @json($dateData)
                    }
                ],
                "yAxis": [
                    {
                        "show": true,
                        "scale": false,
                        "nameLocation": "end",
                        "nameGap": 15,
                        "gridIndex": 0,
                        "inverse": false,
                        "offset": 0,
                        "splitNumber": 5,
                        "minInterval": 0,
                        "splitLine": {
                            "show": false,
                            "lineStyle": {
                                "show": true,
                                "width": 1,
                                "opacity": 1,
                                "curveness": 0,
                                "type": "solid"
                            }
                        }
                    }
                ],
                "title": [
                    {
                        "text": "DAU",
                        "padding": 5,
                        "itemGap": 10
                    }
                ],
                "toolbox": {
                    "show": true,
                    "orient": "horizontal",
                    "itemSize": 15,
                    "itemGap": 10,
                    "left": "80%",
                    "feature": {
                        "saveAsImage": {},
                        "restore": {},
                        "dataView": {},
                        "magicType": {
                            "type": [
                                "line",
                                "bar"
                            ]
                        }
                    }
                },
                "dataZoom": [
                    {
                        "show": true,
                        "type": "slider",
                        "realtime": true,
                        "start": {{$zoomStart}},
                        "end": 100,
                        "orient": "horizontal",
                        "zoomLock": false,
                        "filterMode": "filter"
                    },
                    {
                        "show": true,
                        "type": "inside",
                        "realtime": true,
                        "start": {{$zoomStart}},
                        "end": 100,
                        "orient": "horizontal",
                        "zoomLock": false,
                        "filterMode": "filter"
                    }
                ]
            };
            chart_dau.setOption(option);
            window.addEventListener('resize', function(){
                chart_dau.resize();
            })
            var autoContainer = function () {
                $('#dau').height(250);
                chart_dau.resize();
            };
            autoContainer();
            setTimeout(function() {
                window.location.reload();
            }, 600000);
        });

    </script>
    <style>
        .title{
            height: 250px;
        }
        .div-title{
            text-align: center;
            padding-top: 50px;
            background: url("https://helloo.backstage.mantouhealth.com/plugin/layui/images/goal/path.png") no-repeat 15px center;
        }
        .layui-pond{
            width: 100%;
            height: 480px;
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
        }
        .layui-middle{
            width: 100%;
            height: 235px;
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
        }

        .content{
            font-family: 'Lato',sans-serif;
            text-align: center;
            text-align: center;
            margin: -.2em 0 0 0;
            color: #eee;
            font-size: 2em;
            font-weight: 300;
            height: 100%;
            overflow: hidden;
        }

        .parallax > use {
            animation: move-forever 12s linear infinite;
        }
        .parallax > use:nth-child(1) {
            animation-delay: -2s;
        }
        .parallax > use:nth-child(2) {
            animation-delay: -2s;
            animation-duration: 5s;
        }
        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 3s;
        }

        @keyframes move-forever {
            0% {
                transform: translate(-90px, 0%);
            }
            100% {
                transform: translate(85px, 0%);
            }
        }


        .number{
            padding-top: 70px;
        }
        .number-middle{
            padding-top: 30px;
        }
        .layui-item-title{
            text-align: center;
            padding-top: 10px;
            position: absolute;
            width: 100%;
        }











        .layui-panel-operate{
            box-shadow: 0px 0px 4px 3px #FFD88A;
        }
        .layui-panel-hr{
            box-shadow: 0px 0px 4px 3px #FF8FFF;
        }
        .layui-panel-retention{
            margin-bottom: 10px;
            box-shadow: 0px 0px 4px 3px #FF8F8F;
        }
        .layui-panel-mask{
            margin-top: 10px;
            box-shadow: 0px 0px 4px 3px #FF8F8F;
        }
        .layui-panel-dev{
            box-shadow: 0px 0px 4px 3px #B4FF8F;
        }
        .layui-panel-data{
            box-shadow: 0px 0px 4px 3px #8FDAFF;
        }
        .bottom{
            margin-top: 20px;
        }



        .editorial-operate{
            padding-top: {{$operData['marginTop']}};
        }
        .editorial-hr{
            padding-top: {{$hrData['marginTop']}};
        }
        .editorial-retention{
            padding-top: {{$prodRetentionData['marginTop']}};
        }
        .editorial-mask{
            padding-top: {{$prodMaskData['marginTop']}};
        }
        .editorial-dev{
            padding-top: {{$devData['marginTop']}};
        }
        .editorial-data{
            padding-top: {{$dataData['marginTop']}};
        }



        .content-operate{
            background-color: #FFDA8F;
        }
        .content-hr{
            background-color: #FF8FFF;
        }
        .content-retention{
            background-color: #FF8F8F;
        }
        .content-mask{
            background-color: #FF8F8F;
        }
        .content-dev{
            background-color: #B4FF8F;
        }
        .content-data{
            background-color: #8FDAFF;
        }




        .operate-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group843.png") no-repeat center center;
        }
        .hr-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group844.png") no-repeat center center;
        }
        .dev-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group846.png") no-repeat center center;
        }
        .data-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group847.png") no-repeat center center;
        }






        .layui-icon-div{
            display: flex;
            text-align: center;
            z-index: 999;
            height: 175px;
            background-size: 25% 50%;
            margin: 0 auto;
            position: absolute;
            width: 100%;
        }
        .layui-icon-div-middle{
            display: flex;
            text-align: center;
            z-index: 999;
            height: 80px;
            background-size: 25% 50%;
            margin: 0 auto;
            position: absolute;
            width: 100%;
        }


        @-webkit-keyframes rotate {
            50% {
                transform: translate(-50%, -73%) rotate(180deg);
            }
            100% {
                transform: translate(-50%, -70%) rotate(360deg);
            }
        }

        @keyframes rotate {
            50% {
                transform: translate(-50%, -73%) rotate(180deg);
            }
            100% {
                transform: translate(-50%, -70%) rotate(360deg);
            }
        }
        .layui-panel-middle{
            margin-top: -50px;
        }
        .dev-title-middle{
            height: 200px;
            width: 100%;
            display:flex;
            justify-content:center;
            /*align-items:center;*/
            margin-top: -100px;
        }
        @media screen and (max-width: 750px){
            .dev-title-middle{
                margin-top: 10px;
            }
        }
        .dev-title-middle .wave-num{
            width: 100%;
            height:100px;
            overflow:hidden;
            -webkit-border-radius:50%;
            border-radius:50%;
            text-align:center;
            display:table-cell;
            vertical-align:middle;
            position:absolute;
            z-index:5;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            margin-top: -5px;
        }
        .dev-title-middle .wave-num b{
            color:#fff;
            font-size:24px;
            text-align:center;
            display:block;
            position:relative;
            z-index:2;
            line-height:45px;
        }

        .wave-n{
            width:120px;
            height:120px;
            webkit-border-radius:25em;
            -moz-border-radius:25em;
            border-radius:25em;
            background:#5576ac;
            overflow:hidden;
            position:relative;
            margin-right: 0;
            margin-left: 0;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            box-shadow: 0px 0px 20px 7px #5576AC;
        }

        .wave{
            width:408px;
            height: 80%;
            position:absolute;
            left:0px;
            bottom:0;
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/wave.png") no-repeat;animation: move_wave 1s linear infinite;
            -webkit-animation: move_wave 1s linear infinite;
        }



        @-webkit-keyframes move_wave {
            0% {
                -webkit-transform: translateX(0)
            }
            50% {
                -webkit-transform: translateX(-25%)
            }
            100% {
                -webkit-transform: translateX(-50%)
            }
        }

        @keyframes move_wave {
            0% {
                transform: translateX(0)
            }
            50% {
                transform: translateX(-25%)
            }
            100% {
                transform: translateX(-50%)
            }
        }
        .target-content{
            position:absolute;
            padding: 5px;
            top:150px;
            font-style: inherit;
        }

    </style>
@endsection
