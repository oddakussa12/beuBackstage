@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-switch { width: 50px;}
        .layui-input, .layui-form-select, .layui-form-selected{width: 100px;}
        .layui-edge {right:0;left: 77px;}
        .layui-input-block {padding:0px;margin-left: 10px}
        .layui-form-switch {height: 30px; line-height: 30px; width: 60px;}
        .layui-form-switch i {top: 7px;}
        .text-right {text-align: left; width: 35%}
    </style>
    <div  class="layui-fluid">
        <div style="width: 35%; float: left;">
            <section id="videoPlayer">
                <video id="my-player" style="width: 100%" class="video-js" controls muted data-setup='{"autoplay": true,"preload": "true"}'>
                        <source src="https://media.helloo.cn.mantouhealth.com/{{$messages['message_content']}}"></source>
                    <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>
                </video>
                <input type="hidden" value="{{$page}}" name="page" id="page">
            </section>
        </div>
        <div style="width:59%; float: left; margin-left: 1%;">
            <table style="margin: 0px;" class="layui-table" lay-filter="post_table">
                <tr>
                    <td class="text-right">Choose Month </td>
                    <td>
                        <form class="layui-form">
                            <select style="width: 100px;" name="month" lay-filter="month">
                                @for($i=1; $i<=date('m'); $i++)
                                    <option value="{{$i}}" @if(!empty($month) && $i==$month) selected @endif>{{$i}}</option>
                                @endfor
                            </select>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">AutoPlay</td>
                    <td>
                        <form class="layui-form"><input type="checkbox" name="auto" lay-filter="auto" lay-skin="switch" lay-text="ON|OFF"></form>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">
                        <button style="float: left;" type="button" class="layui-btn layui-btn-sm" id="left"><i class="layui-icon layui-icon-left"></i></button>
                        <button style="float: left;" type="button" class="layui-btn layui-btn-sm" id="prev"><i class="layui-icon layui-icon-prev"></i></button>
                        <input  style="float: left; width: 40px; height: 30px; margin-left: 10px; " type="text" name="prev" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                    </td>
                    <td>
                        <input style="width: 40px; height: 30px; margin-right: 10px; float: left;" type="text" name="next" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                        <button type="button" class="layui-btn layui-btn-sm" id="next"><i class="layui-icon layui-icon-next"></i></button>
                        <button type="button" class="layui-btn layui-btn-sm" id="right"><i class="layui-icon layui-icon-right"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">ID</td>
                    <td><span id="userId">{{$from->user_id}}</span></td>
                </tr>
                <tr>
                    <td class="text-right">User Name</td>
                    <td><span id="userName">{{$from->user_name}}</span></td>
                </tr>
                <tr>
                    <td class="text-right">User Nick Name</td>
                    <td><span id="userNickName">{{$from->user_nick_name}}</span></td>
                </tr>
                <tr>
                    <td class="text-right">Send Time</td>
                    <td><span id="createdAt">{{$messages['created_at']}}</span></td>
                </tr>
                <tr>
                    <td colspan="2">Remark Column</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <form class="layui-form">
                            <input type="hidden" id="id" name="id" value="{{$messages['id']}}">
                            <textarea id="comment" name="comment" style="width: 100%;height: 78px; padding: 5px;">{{$messages['comment']}}</textarea>
                            <button style="float: left;" type="button" class="layui-btn layui-btn-sm" id="submit">Submit</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>


@endsection
@section('footerScripts')
    @parent
    <style>
        #my-player{
            width: 400px;
            height: 500px;
            margin: 0 auto;
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
                // 'loop':true
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
                    if(Storage.get("auto")==='on') {
                        document.getElementById("right").click();
                    }
                })

            }).ready(function(){
                this.play();
            });

            $('#left').on('click' , function(){
                var page = parseInt($("input[name='prev']").val())-1;
                var month = parseInt($("select[name='month']").val());
                get(page, month);
                window.history.pushState(null, null, "?page="+page+"&month="+month);
            });

            $('#prev').on('click' , function(){
                var page = parseInt($("input[name='prev']").val());
                var month = parseInt($("select[name='month']").val());
                get(page, month);
                window.history.pushState(null, null, "?page="+page+"&month="+month);
            });

            $('#next').on('click' , function(){
                let page = parseInt($("input[name='next']").val());
                var month = parseInt($("select[name='month']").val());
                get(page, month);
                window.history.pushState(null, null, "?page="+page+"&month="+month);
            });

            $('#right').on('click' , function(){
                let page = parseInt($("input[name='next']").val())+1;
                let month = parseInt($("select[name='month']").val());
                get(page, month);
            });
            $('#submit').on('click' , function(){
                let id = $("#id").val();
                let comment = $("#comment").val();
                common.ajax("/backstage/lovbee/message/comment", {"message_id":id,"comment":comment} , function(res){
                    console.log(res);
                    layer.closeAll();
                } , 'POST');
            });

            form.on('select(month)', function(obj){
                var month = obj.value;
                let page = GetQueryString('page');
                get(page, month, obj);
            });

            form.on('switch(auto)', function(data){
                if (data.elem.checked) {
                    Storage.set('auto' , 'on');
                } else {
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

                });
            }
            if (Storage.get("auto")==='on') {
                $("input[name='auto']").attr("checked", true);
                form.render();
            }

            var updateUser = function (user){
                $('#userId').text(user.user_id);
                $('#userName').text(user.user_name);
                $('#userNickName').text(user.user_nick_name);
                table.render();
            };
            var updateTime = function (result) {
                $('#id').val(result.id);
                $('#createdAt').text(result.created_at);
                $('#comment').text(result.comment);
            }
            function get(page, month, obj='', select=0) {
                common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month , {} , function(res){
                    layer.closeAll();
                    console.log(res);
                    if(JSON.stringify(res.messages) !== '[]') {
                        let result = res.messages.result;
                        let uri    = result.message_content;
                        let player = videojs('my-player');

                        player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                        player.play();
                        updateTime(result);
                    } else {
                        if (select==1) {
                            let beforeMonth = GetQueryString('month');
                            $(obj.elem).val(beforeMonth);
                            layui.form.render('select');
                        }
                    }
                    if (JSON.stringify(res.from) !== '[]') {
                        updateUser(res.from);
                    }

                    $("input[name='prev']").val(page);
                    $("input[name='next']").val(page);
                } , 'GET');
            }

        });
    </script>
@endsection