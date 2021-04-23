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
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/dev1@10x.png" height="25px">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-operate">
                    <span class="operate-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">4.5K<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">50K<br />Target</h2>
                        </div>
                    </span>
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
                        <span>内容</span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/2@10x.png" height="25px">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-hr">
                    <span class="hr-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">4.5K<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">50K<br />Target</h2>
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
                        <span>内容</span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title-middle">
                    <div class="container">
                        <div class="wave"></div>
                    </div>
                </div>
                <div class="layui-panel layui-panel-middle">
                    <div class="layui-panel layui-slider layui-middle layui-panel-prod">
                        <span class="prod-icon layui-row layui-icon-div-middle">
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">4.5K<br />Reach</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">Retention</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">50K<br />Target</h2>
                            </div>
                        </span>
                        <svg class="editorial-dev"
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
                        <div class="content content-prod">
                            <span>内容</span>
                        </div>
                    </div>
                    <div class="layui-panel layui-slider layui-middle layui-panel-mask">
                        <span class="mask-icon layui-row layui-icon-div-middle">
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">4.5K<br />Reach</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">Retention</h2>
                            </div>
                            <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                                <h2 class="number-middle">50K<br />Target</h2>
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
                            <span>内容</span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/4@10x.png" height="25px">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-dev">
                    <span class="dev-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">4.5K<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">50K<br />Target</h2>
                        </div>
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
                        <span>内容</span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="dev-title">
                    <img src="{{url('plugin/layui/images/goal')}}/5@10x.png" height="25px">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-data">
                    <span class="data-icon layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">4.5K<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">50K<br />Target</h2>
                        </div>
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
                        <span>内容</span>
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
        }).use(['common' , 'layer' , 'element' , 'slider' , 'echarts'], function () {
            var $ = layui.jquery,
                common = layui.common,
                layer = layui.layer,
                slider = layui.slider,
                element = layui.element,
                echarts = layui.echarts;
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
                        "data": [
                            [
                                "2021-02-26",
                                741.0
                            ],
                            [
                                "2021-02-27",
                                733.0
                            ]
                        ],
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
                        "data": [
                            [
                                "2021-02-26",
                                null
                            ],
                            [
                                "2021-02-27",
                                null
                            ]
                        ],
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
                        "data": [
                            "2021-02-26",
                            "2021-02-27"
                        ]
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
                        "start": 30,
                        "end": 50,
                        "orient": "horizontal",
                        "zoomLock": false,
                        "filterMode": "filter"
                    },
                    {
                        "show": true,
                        "type": "inside",
                        "realtime": true,
                        "start": 20,
                        "end": 80,
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
            setTimeout(function() {
                window.location.reload();
            }, 600000);
        })
    </script>
    <style>
        .title{
            height: 500px;
        }
        .dev-title{
            text-align: center;
            background: url("https://helloo.backstage.mantouhealth.com/plugin/layui/images/goal/path.png") no-repeat 15px center;
        }
        .dev-title img{
            height: 36px;
            margin:0 auto;
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











        .layui-panel-operate{
            box-shadow: 0px 0px 4px 3px #FFD88A;
        }
        .layui-panel-hr{
            box-shadow: 0px 0px 4px 3px #FF8FFF;
        }
        .layui-panel-prod{
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
            margin-top: 100px;
        }



        .editorial-operate{
            padding-top: 50px;
        }
        .editorial-operate{
            padding-top: 50px;
        }
        .editorial-dev{
            padding-top: 50px;
        }
        .editorial-data{
            padding-top: 50px;
        }



        .content-operate{
            background-color: #FFDA8F;
        }
        .content-hr{
            background-color: #FF8FFF;
        }
        .content-dev{
            background-color: #B4FF8F;
        }
        .content-prod{
            background-color: #FF8F8F;
        }
        .content-mask{
            background-color: #FF8F8F;
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
        }
        .layui-icon-div-middle{
            display: flex;
            text-align: center;
            z-index: 999;
            height: 80px;
            background-size: 25% 50%;
            margin: 0 auto;
        }

        .container {
            width: 100px;
            height: 100px;
            padding: 1px;
            /*border: 5px solid #76daff;*/
            transform: translate(0%, -80%);
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto;
            box-shadow: 0px 0px 10px 4px #8fdaff;

        }

        .wave {
            width: 100px;
            height: 100px;
            background-color: #76daff;
            border-radius: 50%;
        }
        .wave::before, .wave::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            top: 0;
            left: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 45%;
            transform: translate(-50%, -70%) rotate(0);
            -webkit-animation: rotate 6s linear infinite;
            animation: rotate 6s linear infinite;
            z-index: 10;
        }
        .wave::after {
            border-radius: 47%;
            background-color: rgba(255, 255, 255, 0.9);
            transform: translate(-50%, -70%) rotate(0);
            -webkit-animation: rotate 10s linear -5s infinite;
            animation: rotate 10s linear -5s infinite;
            z-index: 20;
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
            margin-top: -90px;
            z-index: -9999;
        }
        .dev-title-middle{
            z-index: 9999;
        }

    </style>
@endsection
