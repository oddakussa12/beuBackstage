@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table, .layui-table-view, .layui-form-item,.layui-form-switch { margin:0;}
    </style>
    <div  class="layui-fluid">
        <div class="layui-row">
            <div class="layui-col-md4">
                <section id="videoPlayer">
                    <video id="my-player" style="width: 100%" class="video-js" controls muted data-setup='{"autoplay": true,"preload": "true"}'>
                        <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                        </p>
                    </video>
                    <input type="hidden" value="" name="page" id="page">
                </section>
            </div>
            <div class="layui-col-md8">
                <table class="layui-table">
                    <tr>
                        <td>Choose Date</td>
                        <td>
                            <form id="exportForm" method="get" action="/backstage/service/message/export" class="layui-form">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <input  readonly type="text" class="layui-input" name="dateTime" id="dateTime" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$dateTime}}">
                                    </div>
                                    <div class="layui-inline">
                                        <button type="button" class="layui-btn layui-btn-sm" name="export" id="export">Export</button>
                                    </div>

                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Choose Month </td>
                        <td>
                            <form class="layui-form">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline">
                                        <select name="month" lay-filter="month">
                                            @for($i=1; $i<=date('m'); $i++)
                                                <option value="{{$i}}" @if(!empty($month) && $i==$month) selected @endif>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>AutoPlay</td>
                        <td>
                            <form class="layui-form">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline">
                                        <input type="checkbox" name="auto" lay-filter="auto" lay-skin="switch" lay-text="ON|OFF">
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form class="layui-form">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="width: 50px;margin: 4px;">
                                        <button  type="button" class="layui-btn layui-btn-sm" id="left"><i class="layui-icon layui-icon-left"></i></button>
                                    </div>
                                    <div class="layui-input-inline" style="width: 50px;margin: 4px;">
                                        <button  type="button" class="layui-btn layui-btn-sm" id="prev"><i class="layui-icon layui-icon-prev"></i></button>
                                    </div>
                                    <div class="layui-input-inline" style="margin: 0px;">
                                        <input  name="prev" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="">
                                    </div>
                                </div>

                            </form>
                        </td>
                        <td>
                            <form class="layui-form">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="margin: 0px;">
                                        <input  name="next" lay-verify="required" placeholder="1" autocomplete="off" class="layui-input" value="">
                                    </div>
                                    <div class="layui-input-inline" style="width: 50px;margin: 4px;">
                                        <button type="button" class="layui-btn layui-btn-sm" id="next"><i class="layui-icon layui-icon-next"></i></button>
                                    </div>
                                    <div class="layui-input-inline" style="width: 50px;margin: 4px;">
                                        <button type="button" class="layui-btn layui-btn-sm" id="right"><i class="layui-icon layui-icon-right"></i></button>
                                    </div>

                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>ID</td>
                        <td><span id="userId"></span></td>
                    </tr>
                    <tr>
                        <td>User Name</td>
                        <td><span id="userName"></span></td>
                    </tr>
                    <tr>
                        <td>User Nick Name</td>
                        <td><span id="userNickName"></span></td>
                    </tr>
                    <tr>
                        <td>Send Time</td>
                        <td><span id="createdAt"></span></td>
                    </tr>
                    <tr>
                        <td colspan="2">Remark Column</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <form class="layui-form">
                                <input type="hidden" id="id" name="id" value="">
                                <textarea id="comment" name="comment" style="width: 100%;height: 53px; padding: 5px;"></textarea>
                                <button style="float: left;" type="button" class="layui-btn layui-btn-sm" id="submit">Submit</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
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
        //??????????????????
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
            $(document).keyup(function(event){
                if(event.keyCode ===39){
                    $('#right').click();
                }else if(event.keyCode ===37)
                {
                    $('#left').click();
                }
            });
            const Storage = {};
            Storage.get = function(name) {
                return localStorage.getItem(name);
            };
            Storage.set = function(name, val) {
                localStorage.setItem(name, val);
            };
            layer.ready(function(){
                let month = Storage.get('month');
                let page  = Storage.get('page');
                let date  = new Date();
                month = month!==null ? month : date.getMonth()+1;
                page  = page!==null  ? page  : 1;
                $("select[name='month']").val(month);
                $("input[name='prev']").val(page);
                $(" [name='next']").val(page);
                form.render();
                get(page, month);

            });

            let player = videojs('my-player', {
                "autoplay":true,
                "controls": true,
                "preload":"auto",
                // 'loop':true
            }, function() {
                // ??????
                this.on('play', function() {
                    console.log(1);
                });

                //??????
                this.on('pause', function() {
                    console.log(2);
                });

                // ??????
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
                common.ajax("/backstage/service/message/comment", {"message_id":id,"comment":comment} , function(res){
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
                    //??????????????????
                    return decodeURI(r[2]);
                }
                return 1;
            }

            if (window.history && window.history.pushState) {
                //?????????????????????????????????
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
                common.ajax("/backstage/service/message/video?page="+page+"&month="+month , {} , function(res){
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
                        if (select===1) {
                            let beforeMonth = GetQueryString('month');
                            $(obj.elem).val(beforeMonth);
                            layui.form.render('select');
                        }
                    }
                    if (JSON.stringify(res.from) !== '[]') {
                        updateUser(res.from);
                    }
                } , 'GET');
                $("input[name='prev']").val(page);
                $("input[name='next']").val(page);
                Storage.set('month' , month);
                Storage.set('page' , page);
                window.history.pushState(null, null, "?page="+page+"&month="+month);

            }

        });
    </script>
@endsection