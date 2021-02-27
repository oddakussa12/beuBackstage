@extends('layouts.dashboard')
@section('layui-content')
    <div id="QPlayer">
        <div id="pContent">
            <div id="player">
                <span class="cover"></span>
                <div class="ctrl">
                    <div class="musicTag marquee">
                        <strong>Title</strong>
                        <span> - </span>
                        <span class="artist">Artist</span>
                    </div>
                    <div class="progress">
                        <div class="timer left">0:00</div>
                        <div class="contr">
                            <div class="rewind icon"></div>
                            <div class="playback icon"></div>
                            <div class="fastforward icon"></div>
                        </div>
                        <div class="right">
                            <div class="liebiao icon"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ssBtn">
                <div class="adf"></div>
            </div>
        </div>
        <ol id="playlist"></ol>
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
    <link href="/plugin/layui/style/player.css" rel="stylesheet">
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            music: 'lay/modules/music'
        }).use(['common' , 'table', 'layer', 'laydate', 'element' , 'music'], function () {
            let $=layui.jquery,
                form = layui.form,
                layer = layui.layer,
                common = layui.common,
                laydate = layui.laydate;
            var	playlist = [
                {title:"Gravity",artist:"Jessica",mp3:"http://p2.music.126.net/lkK28FliZQJwQ5r1XAZ-KA==/3285340747760477.mp3",cover:"http://p4.music.126.net/7VJn16zrictuj5kdfW1qHA==/3264450024433083.jpg?param=106x106"}
                ];
            var isRotate = true;
            var autoplay = false;
            function bgChange(){
                var lis= $('.lib');
                for(var i=0; i<lis.length; i+=2)
                    lis[i].style.background = 'rgba(246, 246, 246, 0.5)';
            }
            window.onload = bgChange;
        });
    </script>
@endsection