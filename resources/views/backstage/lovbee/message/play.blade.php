@extends('layouts.app')
@section('content')
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>Play</legend>
    </fieldset>
    <div class="layui-container">

        <div class="layui-row">
                <div class="layui-col-md4 layui-col-md-offset4">
                    <form class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">Auto play next</label>
                            <div class="layui-input-block">
                                <input type="checkbox" name="auto" lay-filter="auto" lay-skin="switch" lay-text="ON|OFF">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md4">
                </div>
        </div>


        <div class="layui-row">
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">Month</label>
                    <div class="layui-input-block">
                        <select name="month" lay-filter="month">
                            @for($i=1; $i<13; $i++)
                                <option value="{{$i}}" @if(!empty($month) && $i==$month) selected @endif>{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-row layui-col-space4">
            <div class="layui-col-md1">
                <input type="text" name="prev" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                <button type="button" class="layui-btn layui-btn-sm" id="left">
                    <i class="layui-icon layui-icon-left"></i>
                </button>
                <button type="button" class="layui-btn layui-btn-sm" id="prev">
                    <i class="layui-icon layui-icon-prev"></i>
                </button>
            </div>
            <div class="layui-col-md10">

                <section id="videoPlayer">
                    <video
                            id="my-player"
                            class="video-js"
                            controls
                            muted
                            data-setup='{"autoplay": true,"preload": "true"}'>

                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank">
                                supports HTML5 video
                            </a>
                        </p>
                    </video>
                    <input type="hidden" value="{{$page}}" name="page" id="page">
                </section>

            </div>
            <div class="layui-col-md1">
                <input type="text" name="next" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                <button type="button" class="layui-btn layui-btn-sm" id="next">
                    <i class="layui-icon layui-icon-next"></i>
                </button>
                <button type="button" class="layui-btn layui-btn-sm" id="right">
                    <i class="layui-icon layui-icon-right"></i>
                </button>
            </div>
        </div>
    </div>
@endsection
@section('footerScripts')
    @parent
    <style>
        #my-player{
            width: 800px;
            height: 400px;
        }
    </style>
    <link href="//vjs.zencdn.net/7.10.2/video-js.min.css" rel="stylesheet">
    <script src="//vjs.zencdn.net/7.10.2/video.min.js"></script>
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            formSelects: 'lay/modules/formSelects-v4',
        }).use(['common' , 'table' , 'layer' , 'carousel' , 'element' , 'upload' , 'loadBar' , 'formSelects'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                loadBar = layui.loadBar,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery,
                formSelects=layui.formSelects,
                upload = layui.upload;
                formSelects.render('country');
                const Storage = {};

                Storage.get = function(name) {
                    return localStorage.getItem(name);
                };

                Storage.set = function(name, val) {
                    localStorage.setItem(name, val);
                };
                form.on('select(type)', function (data) {
                    if (data.value == 0||data.value == 3) {
                        $("#targetId").attr("disabled", "true");
                        $("#targetIdDiv").hide();
                        $("#country").attr("disabled", "true");
                        $("#targetId").removeAttr("lay-verify");
                        $("#countryDiv").hide();
                        form.render('select');
                    }else if (data.value == 1) {
                        $("#targetId").attr("disabled", "true");
                        $("#targetIdDiv").hide();
                        $("#country").removeAttr("disabled");
                        $("#targetId").removeAttr("lay-verify");
                        $("#countryDiv").show();
                        form.render('select');//select是固定写法 不是选择器
                    } else {
                        $("#targetId").removeAttr("disabled");
                        $("#targetIdDiv").show();
                        $("#country").attr("disabled", "true");
                        $("#targetId").attr("lay-verify", "required");
                        $("#countryDiv").hide();
                        form.render('select');

                    }
                });

                var player = videojs('my-player', {
                    "autoplay":true,
                    "controls": true,
                    "preload":"auto",
                    "resource":[
                        @foreach($messages as $message)
                        "src":"https://media.helloo.cn.mantouhealth.com/{{$message->message_content}}"
                        @endforeach
                    ]
                }, function() {
                    // 播放
                    this.on('play', function() {
                        console.log(1);
                    });

                    //暂停
                    this.on('pause', function() {
                        console.log(2);
                    });

                    // 结束
                    this.on('ended', function() {
                        if(Storage.get("auto")==='on')
                        {
                            document.getElementById("right").click();
                        }
                    })

                }).ready(function(){
                    this.play();
                });

                $('#left').on('click' , function(){
                    var page = parseInt($("input[name='prev']").val())-1;
                    var month = parseInt($("select[name='month']").val());
                    common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month , {} , function(res){
                        layer.closeAll();
                        if(JSON.stringify(res) !== '[]')
                        {
                            let uri = res[0].message_content;
                            let player =  videojs('my-player');
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                            player.play()
                        }
                        $("input[name='prev']").val(page);
                        $("input[name='next']").val(page);
                    } , 'GET');
                    window.history.pushState(null, null, "?page="+page+"&month="+month);
                });

                $('#prev').on('click' , function(){
                    var page = parseInt($("input[name='prev']").val());
                    var month = parseInt($("select[name='month']").val());
                    common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month , {} , function(res){
                        layer.closeAll();
                        if(JSON.stringify(res) !== '[]')
                        {
                            let uri = res[0].message_content;
                            let player =  videojs('my-player');
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                            player.play()
                        }
                        $("input[name='prev']").val(page);
                        $("input[name='next']").val(page);
                    } , 'GET');
                    window.history.pushState(null, null, "?page="+page+"&month="+month);
                });

                $('#next').on('click' , function(){
                    let page = parseInt($("input[name='next']").val());
                    var month = parseInt($("select[name='month']").val());
                    common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month  , {} , function(res){
                        layer.closeAll();
                        if(JSON.stringify(res) !== '[]')
                        {
                            let uri = res[0].message_content;
                            let player =  videojs('my-player');
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                            player.play()
                        }
                        $("input[name='next']").val(page);
                        $("input[name='prev']").val(page);
                    } , 'GET');
                    window.history.pushState(null, null, "?page="+page+"&month="+month);
                });

                $('#right').on('click' , function(){
                    let page = parseInt($("input[name='next']").val())+1;
                    var month = parseInt($("select[name='month']").val());
                    common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month  , {} , function(res){
                        layer.closeAll();
                        if(JSON.stringify(res) !== '[]')
                        {
                            let uri = res[0].message_content;
                            let player =  videojs('my-player');
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                            player.play()
                        }else{
                            common.prompt('No video anymore');
                        }
                        $("input[name='next']").val(page);
                        $("input[name='prev']").val(page);
                    } , 'GET');
                    window.history.pushState(null, null, "?page="+page+"&month="+month);
                });

                form.on('select(month)', function(obj){
                    var month = obj.value;
                    var beforeMonth = GetQueryString('month');
                    let page = GetQueryString('page');

                    common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month  , {} , function(res){
                        layer.closeAll();
                        if(JSON.stringify(res) !== '[]')
                        {
                            let uri = res[0].message_content;
                            let player =  videojs('my-player');
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                            player.play()
                            $("input[name='next']").val(page);
                            $("input[name='prev']").val(page);
                            window.history.pushState(null, null, "?page="+page+"&month="+month);
                        }else{
                            $(obj.elem).val(beforeMonth);
                            layui.form.render('select');
                        }
                    } , 'GET');
                });

                form.on('switch(auto)', function(data){
                    if(data.elem.checked)
                    {
                        Storage.set('auto' , 'on');
                    }else{
                        Storage.set('auto' , 'off');
                    }
                });

            function GetQueryString(name) {
                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                    var r = window.location.search.substr(1).match(reg);
                    if(r != null){
                        //解决中文乱码
                        return decodeURI(r[2]);
                    }
                    return 1;
                }

                if (window.history && window.history.pushState) {
                    //监听浏览器前进后退事件
                    $(window).on('popstate', function () {
                        var month = GetQueryString('month');
                        let page = GetQueryString('page');
                        common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month  , {} , function(res){
                            layer.closeAll();
                            if(JSON.stringify(res) !== '[]')
                            {
                                let uri = res[0].message_content;
                                let player =  videojs('my-player');
                                player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                                player.play()
                            }
                            $("input[name='next']").val(page);
                            $("input[name='prev']").val(page);
                        } , 'GET');
                    });
                }
                if(Storage.get("auto")==='on')
                {
                    $("input[name='auto']").attr("checked", true);
                    form.render();
                }

        });
    </script>
@endsection