@extends('layouts.dashboard')
@section('layui-content')
    <div id="QPlayer" style="transform: translateX(250px);">
        <div id="pContent">
            <div id="player">
                <span class="cover" >
{{--                    <img src="http://p3.music.126.net/DkVjogF-Ga8_FX0Kf7p7Pw==/2328765627693725.jpg?param=106x106" style="animation: 9.8s linear 0s infinite normal none paused rotate;">--}}
                </span>
                <div class="ctrl">
                    <div class="musicTag marquee">
                        <strong>그대라는 한 사람</strong>
{{--                        <span> - </span>--}}
{{--                        <span class="artist">Jessica</span>--}}
                    </div>
                    <div class="progress">
                        <div class="timer left">0:00</div>
                        <div class="contr">
                            <div class="rewind icon"></div>
                            <div class="playback icon"></div>
                            <div class="forward icon"></div>
                        </div>
                        <div class="right">
                            <div class="playlist icon"></div>
                        </div>
                    </div>
                </div>
                <audio>
{{--                    <source src="http://p2.music.126.net/N5MyzQh73z5KRqhmQe_WPg==/5675679022587512.mp3">--}}
                </audio>
            </div>
            <div class="ssBtn">
                <div class="adf on"></div>
            </div>
        </div>
        <ol id="playlist" style="ooverflow: auto; max-height: 0px; transition: max-height 0.5s ease 0s; border: 0px;" class="">
{{--            <li class="lib" style="overflow: hidden; background: rgba(246, 246, 246, 0.5);">--}}
{{--                <strong style="margin-left: 5px;">Gravity</strong>--}}
{{--                <span style="float: right;" class="artist">Jessica</span>--}}
{{--            </li>--}}
{{--            <li class="lib" style="overflow: hidden; color: rgb(221, 221, 221);">--}}
{{--                <strong style="margin-left: 5px;">-エンディング- 世界の约束 ~ 人生のメリーゴーランド</strong>--}}
{{--                <span style="float: right;" class="artist">久石譲</span>--}}
{{--            </li>--}}
        </ol>
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
                laydate = layui.laydate,
                music = layui.music;
            var	playlist = [
                {
                    title:"Gravity",
                    artist:"Jessica",
                    mp3:"http://p2.music.126.net/lkK28FliZQJwQ5r1XAZ-KA==/3285340747760477.mp3",
                    cover:"http://p4.music.126.net/7VJn16zrictuj5kdfW1qHA==/3264450024433083.jpg?param=106x106"
                }
            ];
            music.init({
                'id':"playlist",
                'player':"player",
                'list':playlist,
            });
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