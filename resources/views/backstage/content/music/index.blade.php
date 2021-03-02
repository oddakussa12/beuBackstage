@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">Music</li>
            <li>Music Play</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <table class="layui-hide" id="music_table" lay-filter="music_table"></table>
            </div>
            <div class="layui-tab-item">
                <audio id="player" src=""></audio>
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
                table = layui.table,
                layer = layui.layer,
                common = layui.common,
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


            var musicList = function (res){
                // var url = '?page='+getQueryString('page');
                // common.ajax(url , {} , function(res){
                //     layer.closeAll();
                //
                // } , 'get');
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

            var tableIns = table.render({
                elem: '#music_table'
                ,url:'/backstage/content/music/index'
                // ,toolbar: true
                ,cols: [[
                    {field:'id', title:'ID', maxWidth:50}
                    ,{field:'name', title:'Name', maxWidth:120 , edit:'text'}
                    ,{field:'hash', title:'Hash', minWidth:120}
                    ,{field:'time', title:'Time', minWidth:120 , edit:'text'}
                    ,{field:'is_delete', title:'Deleted', minWidth:120 ,templet:function(d){
                            if(d.is_delete==1)
                            {
                                return '<input type="checkbox" checked name="is_delete" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">';
                            }else{
                                return '<input type="checkbox"  name="is_delete" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">';
                            }
                        }}
                    ,{field:'recommendation', title:'Recommendation', minWidth:120 ,templet:function(d){
                            if(d.recommendation==1)
                            {
                                return '<input type="checkbox" checked name="recommendation" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">';
                            }else{
                                return '<input type="checkbox"  name="recommendation" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">';
                            }
                        }}
                    ,{field:'sort', title:'Sort', minWidth:120 , edit:'text'}
                    ,{field:'created_at', title:'CreatedAt', minWidth:120}
                ]]
                ,page: true
                ,limit:2
                ,limits:[2]
                ,response: {
                    statusCode: 200
                }
                ,parseData: function(res){
                    console.log(res);
                    var list = [];
                    musicList(res);
                    // var data = res.data;


                    return {
                        "code": 200,
                        "data": res.data,
                        'count':res.total
                    };
                }
            });
            form.render();
            form.on('switch(switchAll)', function(data){
                let params;
                const checked = data.elem.checked;
                const musicId  = data.othis.parents('tr').find("td:eq(0)").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('content::music.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                let name = $(data.elem).attr('name');
                params = checked ? '{"' + name + '":"on"}' : '{"' + name + '":"off"}';
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/content/music')}}/"+musicId , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'PATCH' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });

            //监听单元格编辑
            table.on('edit(music_table)', function(obj){
                var that = this;
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field //得到字段
                    ,original = $(this).prev().text(); //得到字段
                var params = d = {};
                @if(!Auth::user()->can('content::music.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                d[field] = original;
                obj.update(data);
                $(this).val(original);
                table.render();
                return true;
                @endif
                params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/content/music')}}/"+data.id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                    } , 'PATCH' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            obj.update(d);
                            $(that).val(original);
                            table.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                    console.log(d);
                    console.log(original);
                    console.log(original);
                    obj.update(d);
                    $(that).val(original);
                    table.render();
                });
            });

        });
    </script>
@endsection