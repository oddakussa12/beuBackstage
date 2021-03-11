@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-switch { width: 50px;}
        .layui-input, .layui-form-select, .layui-form-selected{width: 100px; height: 30px;}
        .layui-edge {right:0;left: 77px;}
        .layui-input-block {padding:0px;margin-left: 10px}
        .layui-form-switch {width: 60px; margin: 0px;}
        /*.layui-form-switch i {top: 7px;}*/
        .text-right {text-align: left; width: 35%}
    </style>
    <div  class="layui-fluid">
        <div style="width: 35%; float: left;">
            <section id="videoPlayer">
                <video id="my-player" style="width: 100%" class="video-js" controls muted data-setup='{"autoplay": true,"preload": "true"}'>
                    @if(!empty($messages['message_content']))
                        @if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$messages['message_content']))
                        <source src="{{$messages['message_content']}}"></source>
                        @else
                        <source src="https://media.helloo.cn.mantouhealth.com/{{$messages['message_content']}}"></source>
                        @endif
                    @endif
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
                    <td>Choose Date</td>
                    <td>
                        <form id="exportForm" method="get" action="/backstage/lovbee/message/export" class="layui-form">
                            <input style="width: 200px; height: 30px; float: left;" type="text" class="layui-input" name="dateTime" id="dateTime" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$dateTime}}">
                            <button style="margin-left: 20px;" type="button" class="layui-btn layui-btn-sm" name="export" id="export">Export</button>
                        </form>
                    </td>
                </tr>
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
                        <input  style="float: left; width: 40px; height: 30px; margin-left: 10px; " type="text" id="prev" name="prev" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                    </td>
                    <td>
                        <input style="width: 40px; height: 30px; margin-right: 10px; float: left;" type="text" id="next" name="next" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="{{$page}}">
                        <button type="button" class="layui-btn layui-btn-sm" id="next"><i class="layui-icon layui-icon-next"></i></button>
                        <button type="button" class="layui-btn layui-btn-sm" id="right"><i class="layui-icon layui-icon-right"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">ID</td>
                    <td><span id="userId">@if(!empty($from->user_id)){{$from->user_id}}@endif</span></td>
                </tr>
                <tr>
                    <td class="text-right">User Name</td>
                    <td><span id="userName">@if(!empty($from->user_name)){{$from->user_name}}@endif</span></td>
                </tr>
                <tr>
                    <td class="text-right">User Nick Name</td>
                    <td><span id="userNickName">@if(!empty($from->user_nick_name)){{$from->user_nick_name}}@endif</span></td>
                </tr>
                <tr>
                    <td class="text-right">Send Time</td>
                    <td><span id="createdAt">@if(!empty($messages['created_at'])){{$messages['created_at']}}@endif</span></td>
                </tr>
                <tr>
                    <td colspan="2">Remark Column</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <form class="layui-form">
                            <input type="hidden" id="id" name="id" value="@if(!empty($messages['id'])){{$messages['id']}}@endif">
                            <textarea id="comment" name="comment" style="width: 100%;height: 53px; padding: 5px;">@if(!empty($messages['comment'])){{$messages['comment']}}@endif</textarea>
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
        }).use(['common' , 'table', 'layer', 'laydate', 'element' , 'formSelects'], function () {
            let $=layui.jquery,
                form = layui.form,
                layer = layui.layer,
                common = layui.common,
                layDate = layui.laydate;
            layDate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });

            const Storage = {};
            Storage.get = function(name) {
                return localStorage.getItem(name);
            };
            Storage.set = function(name, val) {
                localStorage.setItem(name, val);
            };

            let player = videojs('my-player', {
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
                let page   = parseInt($("input[name='prev']").val())-1;
                let month  = parseInt($("select[name='month']").val());
                get(page, month);
            });

            $('#prev').on('click' , function(){
                let page = parseInt($("input[name='prev']").val());
                let month = parseInt($("select[name='month']").val());
                get(page, month);
            });

            $('#next').on('click' , function(){
                let page = parseInt($("input[name='next']").val());
                let month = parseInt($("select[name='month']").val());
                get(page, month);
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

            $('#export').on('click' , function(){
                $("#exportForm").submit();
            });

            form.on('select(month)', function(obj){
                let month = obj.value;
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
                let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                let r = window.location.search.substr(1).match(reg);
                if(r != null){
                    //解决中文乱码
                    return decodeURI(r[2]);
                }
                return 1;
            }

            if (window.history && window.history.pushState) {
                //监听浏览器前进后退事件
                $(window).on('popstate', function () {
                    let month = GetQueryString('month');
                    let page = GetQueryString('page');
                    get(page, month);
                });
            }
            if (Storage.get("auto")==='on') {
                $("input[name='auto']").attr("checked", true);
                form.render();
            }

            let updateUser = function (user){
                $('#userId').text(user.user_id);
                $('#userName').text(user.user_name);
                $('#userNickName').text(user.user_nick_name);
            };
            let updateTime = function (result) {
                $('#id').val(result.id);
                $('#createdAt').text(result.created_at);
                $('#comment').text(result.comment);
            }
            function get(page, month, num=0, obj='', select=0) {
                common.ajax("/backstage/lovbee/message/video?page="+page+"&month="+month , {} , function(res){
                    layer.closeAll();
                    console.log(res);
                    if(JSON.stringify(res.messages) !== '[]') {
                        let result = res.messages.result;
                        let uri    = result.message_content;
                        if (uri===undefined) {
                            return;
                        }
                        let player = videojs('my-player');
                        let protocol = uri.substr(0,5).toLowerCase();
                        if (protocol=='https'||protocol=='http') {
                            player.src(uri);
                        }else{
                            player.src("https://media.helloo.cn.mantouhealth.com/"+uri);
                        }
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
                Storage.set('month' , month);
                Storage.set('page' , page);
                window.history.pushState(null, null, "?page="+page+"&month="+month);

            }

        });
    </script>
@endsection