@extends('layouts.dashboard')
@section('layui-content')
    <audio id="player" src="https://qneventsource.mmantou.cn/b14fb0b27d8ff0cc44cf6c9a76a2c0f6.mp3"></audio>
    <div id="bz_music" class="bz_music">
        <div class="music_info">
            <div class="left_photo">
                <a href="javascript:;">
                    <img id="left_photo" src="/plugin/layui/images/music/vinyl.png" alt="" /></a>
            </div>
            <div class="center_list">
                <ul>
                    <li class="list_current">
                        <a href="javascript:;">
                            <span id="list_title" class="list_title"></span></a>
                        <a href="javascript:;">
                            <span id="list_singer" class="list_singer"></span></a>
                    </li>
                </ul>
            </div>
            <div class="right_btn">
                <a id="btn_prev" href="javascript:;">
                    <span class="layui-icon layui-icon-prev"  style="font-size: 20px; color: #666;"></span>
                </a>
                <a id="btn_list" href="javascript:;">
                    <i id="showHide" class="layui-icon layui-icon-spread-left"  style="font-size: 20px; color: #666;"></i>
                </a>
                <a id="btn_next" href="javascript:;">
                    <i class="layui-icon layui-icon-next"  style="font-size: 20px; color: #666;"></i>
                </a>
            </div>
            <div id="process" class="process">
                <i id="process_slide" class="process_slide"></i>
            </div>
        </div>
        <div class="music_controls">
            <div id="time" class="time">00:00</div>
            <div class="play_controls">
                <a id="btnBackward" href="javascript:;">
                    <i class="layui-icon layui-icon-left" style="font-size: 40px; color: #666;"></i>
                </a>
                <a id="btnPlay" href="javascript:;">
                    <i class="layui-icon layui-icon-play" style="font-size: 40px; color: #666;"></i>
                </a>
                <a id="btnForward" href="javascript:;">
                    <i class="layui-icon layui-icon-right" style="font-size: 40px; color: #666;"></i>
                </a>
            </div>
            <a id="volumeOff" class="volumeOff" href="javascript:;">
                <i class="layui-icon layui-icon-subtraction" style="font-size: 10px; color: #666;"></i>
            </a>
            <div id="volume" class="volume">
                <span id="volume_slide" class="btn_slide"></span>
            </div>
            <a id="volumeOn" class="volumeOn" href="javascript:;">
                <i class="layui-icon layui-icon-addition" style="font-size: 10px; color: #666;"></i>
            </a>
        </div>
        <div id="play_list_area" class="play_list_area">
            <ul id="play_list" class="play_list"></ul>
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
    <link href="/plugin/layui/style/player.css" rel="stylesheet">
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'table', 'layer', 'laydate', 'element'], function () {
            let $=layui.jquery,
                form = layui.form,
                layer = layui.layer,
                common = layui.common,
                laydate = layui.laydate,
                music_list=[],
                play_list = document.querySelector("#play_list"),
                player = document.querySelector("#player"),
                play_index = 0;
            var backward = function (){
                if(play_index==0){
                    play_index=music_list.length-1;
                }
                else{
                    play_index--;
                }
                //重新载入
                loadMusic();
                //播放
                playMusic();
            };
            var forward = function (){
                if(play_index==music_list.length-1){
                    play_index=0;
                }
                else{
                    //改变播放序号
                    play_index++;
                }
                //重新载入
                loadMusic();
                //播放
                playMusic();
            };
            var volumeOff = function (){

            }
            var volumeOn = function () {

            }
            var showMusicList = function () {

            }


            var musicList = function (){
                var url = '?page='+getQueryString('page');
                common.ajax(url , {} , function(res){
                    layer.closeAll();
                    music_list = res.data;
                    for(var i=0;i<music_list.length;i++){
                        //将每个对象，分别存到music中
                        var music = music_list[i];
                        //创建li标签
                        var liTag = document.createElement("li");
                        //创建歌曲名span标签
                        var spanTitleTag = document.createElement("span");
                        //创建时长span标签
                        var spanDurationTag = document.createElement("span");
                        console.log(play_list);
                        //为ul添加li标签，子节点
                        play_list.appendChild(liTag);
                        //为li标签，添加子节点
                        liTag.appendChild(spanTitleTag);
                        liTag.appendChild(spanDurationTag);

                        //添加内容
                        spanTitleTag.innerHTML=music.name;
                        spanDurationTag.innerHTML='00:15';

                        //添加类名
                        spanTitleTag.classList.add("list_title");
                        spanDurationTag.classList.add("list_time");

                        //自定义属性
                        //需要用的时候，直接从标签中取值，不需要和后台交互
                        liTag.setAttribute("data-index",i.toString());

                        //当点击每一个li标签的时候
                        //重新载入歌曲信息(专辑图片、歌曲路径、歌曲名、歌手名)
                        //播放当前点击的音乐
                        liTag.addEventListener("click",function(){
                            //获取每个li标签的歌曲id
                            play_index = this.getAttribute("data-index");

                            //将歌曲id赋给，全局变量play_index
                            loadMusic();
                            playMusic();

                        })
                        //调用载入歌曲函数
                        loadMusic();
                        //播放音乐
                        // player.muted = true;
                        // playMusic();
                    }
                } , 'get');
            }

            var loadMusic = function (){
                var music = music_list[play_index];
                //改变歌曲名
                list_title.innerHTML = music.name;
                //改变歌手名
                list_singer.innerHTML = 'unknown';
                //改变歌曲路径
                player.src = music.url;
            }

            function playMusic(){
                player.play();
                $('#btnPlay').find('i').removeClass('layui-icon-play');
                $('#btnPlay').find('i').addClass('layui-icon-pause');
            }
            function stopMusic(){
                player.pause();
                $('#btnPlay').find('i').removeClass('layui-icon-pause');
                $('#btnPlay').find('i').addClass('layui-icon-play');
            }

            var getQueryString = function (name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if(r != null){
                    //解决中文乱码
                    return decodeURI(r[2]);
                }
                return 1;
            }

            $('#btnBackward').on('click' , backward);
            $('#btnForward').on('click' , forward);
            $('#volumeOff').on('click' , volumeOff);
            $('#volumeOn').on('click' , volumeOn);
            $('#showMusicList').on('click' , showMusicList);
            $('#btnPlay').on("click",function(){
                //paused,表示当前音乐是否为暂停状态
                if(player.paused){
                    playMusic();
                }else {
                    stopMusic();
                }
                console.log(player.paused);
            })
            musicList();

        });
    </script>
@endsection