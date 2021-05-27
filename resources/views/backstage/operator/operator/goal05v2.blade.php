@extends('layouts.app')
@section('content')
    <div  class="layui-fluid">
        <div class="layui-row">
            <div class="layui-col-md1"><div style="padding: 30px;"></div></div>
            <div class="layui-col-md10 title" id="dau"></div>
            <div class="layui-col-md1"><div style="padding: 30px;"></div></div>
        </div>
        <div class="layui-row">
            <div class="dev-title-middle">
                <div class="wave-num"><b id="num">{{$monthDau['current']}}</b><tt>MAU</tt></div>
                <div class="wave-n">
                    <div class="wave" style="height: {{$monthDau['percentage']}};">&nbsp;</div>
                </div>
            </div>
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
                <div class="layui-panel">
                    <div class="layui-panel layui-slider layui-middle layui-panel-operate">
                        <div class="layui-row layui-item-title">
                            <h2>Business Account</h2>
                        </div>
                        <span class="shop-icon layui-row layui-icon-div" style="background-size: 30% 50%;">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
{{--                            <h2 class="number">{{$shopData['current']}}<br />Reach</h2>--}}
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;">
                                <h2 style="margin-top: 70px;" class="number">{{$shopData['current']}}<br />Reach</h2>
                            </div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
{{--                            <h2 class="number">{{$shopData['goal']}}<br />Target</h2>--}}
                        </div>
                    </span>
                        <svg class="editorial-shop"
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
                                <use xlink:href="#gentle-wave-operate" x="50" y="0" fill="#FFD24A"></use>
                                <use xlink:href="#gentle-wave-operate" x="50" y="1" fill="#FFEABF"></use>
                                <use xlink:href="#gentle-wave-operate" x="50" y="2" fill="#FFDA8F"></use>
                            </g>
                        </svg>
                        <div class="content content-operate">
                            <span></span>
                        </div>
                    </div>
                    <div class="layui-panel layui-slider layui-middle layui-panel-operate margin-top-10">
                        <div class="layui-row layui-item-title">
                            <h2>NewUser</h2>
                        </div>
                        <span class="layui-row layui-icon-div">
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$newUserData['current']}}<br />Reach</h2>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$newUserData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                        <svg class="editorial editorial-newUser" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none">
                            <defs>
                                <path id="gentle-wave-operate" d="M-160 44c30 0
    58-18 88-18s
    58 18 88 18
    58-18 88-18
    58 18 88 18
    v44h-352z"></path>
                            </defs>
                            <g class="parallax">
                                <use xlink:href="#gentle-wave-operate" x="50" y="0" fill="#FFD24A"></use>
                                <use xlink:href="#gentle-wave-operate" x="50" y="1" fill="#FFEABF"></use>
                                <use xlink:href="#gentle-wave-operate" x="50" y="2" fill="#FFDA8F"></use>
                            </g>
                        </svg>
                        <div class="content content-operate">
                            <span></span>
                        </div>
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
                <div class="div-title">
                </div>
                <div class="layui-panel layui-slider layui-pond layui-panel-retention">
                    <div class="layui-row layui-item-title">
                        <h2>Products</h2>
                    </div>
                    <span class="prod-icon layui-row layui-icon-div">
                        <div onclick="developer('product')" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number"><span id="product-num">{{$productData['current']}}</span><br />Reach</h2>
                        </div>
                        <div onclick="developer('product',1)" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div onclick="developer('product')" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$productData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <span id="product" class="target-content" >
                        <ul class="site-doc-bgcolor">
                            @foreach($productData['text'] as $text)
                                {!! $text !!}
                            @endforeach
                        </ul>
                    </span>
                    <svg class="editorial-product"
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
                    <div class="content content-product">
                        <span></span>
                    </div>
                    <button id="button1" class="button-1">Show</button>

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
                        <div onclick="developer('developer')" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number"><span id="developer-num">{{$devData['current']}}</span><br />Reach</h2>
                        </div>
                        <div onclick="developer('developer', 1)" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <div style="padding: 5px;"></div>
                        </div>
                        <div onclick="developer('developer')" class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                            <h2 class="number">{{$devData['goal']}}<br />Target</h2>
                        </div>
                    </span>
                    <span id="developer" class="target-content" >
                        <ul class="site-doc-bgcolor">
                            @foreach($devData['text'] as $text)
                                {!! $text !!}
                            @endforeach
                        </ul>
                    </span>
                    <svg class="editorial-developer"
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
                    <button id="button2" class="button-2">Show</button>
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
        function developer(id, state=0) {
            document.getElementById(id).style.display=state === 1 ? 'block' : 'none';
        }
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element', 'common'], function () {
            var $ = layui.jquery,
                common=layui.common;
            $('button').click(function () {
               let id = this.id === 'button1' ? 'product' : 'developer';
                $("#"+id).toggle();
            });
            $('input').click(function () {
                let cName    = this.name;
                let checked = [];
                $('input[name="'+cName+'"]:checked').each(function(){//遍历，将所有选中的值放到数组中
                    checked.push($(this).val());
                });

                common.ajax("{{url('/backstage/operator/operator/goal/data')}}", {'name': cName, 'value': checked}, function(res){
                    let total = res.divName==='product' ? {{$productData['goal']}} : {{$devData['goal']}};
                    let top = ((total-res.current)/total)*480+'px';
                    $("#"+res.divName+"-num").text(res.current);
                    $(".editorial-"+res.divName).css('padding-top', top);
                    layer.closeAll();
                }, 'post');
            });
            setTimeout(function() {
                window.location.reload();
            }, 600000);
        });
    </script>
    <style>

        .layui-col-md2 { width: 20.83%;}
        @media screen and (max-width: 992px){
            .layui-col-md2 {width: auto;}
        }
        .title{
            height: 250px;
        }
        button {
            width: 150px;
            height: 50px;
            border: 0;
            border-radius: 10px;
        }
        .button-1 {
            position: fixed;
            top: 91vh;
            right: 35.5vw;
            background: #ff8f8f;
        }
        .button-2 {
            position: fixed;
            top: 91vh;
            right: 15vw;
            background: #b4ff8f;
        }
        .margin-top-10 {
            margin-top: 10px;
        }
        .div-title{
            text-align: center;
            padding-top: 80px;
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

        .bottom{
            margin-top: 20px;
        }

        .editorial-hr{
            padding-top: {{$hrData['marginTop']}};
        }
        .editorial-shop{
            padding-top: {{$shopData['marginTop']}};
        }
        .editorial-newUser{
            padding-top: {{$newUserData['marginTop']}};
        }
        .editorial-product{
            padding-top: {{$productData['marginTop']}};
        }
        .editorial-developer{
            padding-top: {{$devData['marginTop']}};
        }



        .content-operate{
            background-color: #FFDA8F;
        }
        .content-hr{
            background-color: #FF8FFF;
        }
        .content-product{
            background-color: #FF8F8F;
        }
        .content-dev{
            background-color: #B4FF8F;
        }



        #dau{
            background: url("/plugin/layui/images/goal/banner.png") no-repeat center center; background-size: 100% 100%;
        }
        .operate-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group843.png") no-repeat center center;
        }
        .hr-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group844.png") no-repeat center center;
        }
        .prod-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group845.png") no-repeat center center;
        }
        .dev-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group846.png") no-repeat center center;
        }
        .data-icon{
            background: url("https://test.helloo.backstage.mantouhealth.com/plugin/layui/images/goal/Group847.png") no-repeat center center;
        }
        .shop-icon{
            background: url("/plugin/layui/images/goal/Group848.png") no-repeat center center;
        }

        .layui-icon-div{
            display: flex;
            text-align: center;
            height: 175px;
            background-size: 20% 52%;
            margin: 0 auto;
            position: absolute;
            width: 100%;
        }
        .layui-icon-div-middle{
            display: flex;
            text-align: center;
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
            display:flex;
            justify-content:center;
            align-items:center;
            position: absolute;
            z-index: 11;
            margin-left: 45%;
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
        .dev-title-middle .wave-num tt{
            color: #fff;
            font-size: 20px;
            text-align: center;
            display: block;
            position: relative;
            z-index: 2;
            font-weight: bold;
            width: 50%;
            border-top: 2px #fff solid;
            margin: 3px auto;
            line-height: 25px;
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
            box-shadow: 0 0 20px 7px #5576AC;
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
            top:160px;
            font-style: inherit;
        }
        .target-content input {
            margin-right: 7px;
        }

    </style>
@endsection
