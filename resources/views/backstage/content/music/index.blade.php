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
                <div style="display:block;width:60%;">
                    <form  class="layui-form" lay-filter="music_form">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('music.form.label.name')}}</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="name" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('music.form.label.time')}}</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="time" lay-verify="required" autocomplete="off" value="15" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="layui-upload">
                                <label class="layui-form-label">Upload Music</label>
                                <div class="layui-input-block">
                                    <div class="layui-upload-list">
                                        <table class="layui-table"  lay-filter="table">
                                            <thead>
                                            <tr>
                                                <th lay-data="{field:'name', minWidth:100}">File Name</th>
                                                <th lay-data="{field:'op', width:100}">Operate</th>
                                            </tr>
                                            </thead>
                                            <tbody id="fileList">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button type="button" class="layui-btn" id="music"><i class="layui-icon">???</i>Upload Music</button>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="form_submit_add">{{trans('common.form.button.add')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
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
    <script src="/js/bundle.js"></script>
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
{{--            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>--}}
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>
    <style>
        #my-player{
            width: 400px;
            height: 500px;
            margin: 0 auto;
        }
    </style>
    <link href="/plugin/layui/style/player.css" rel="stylesheet">
    <script>
        //??????????????????
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'table', 'layer', 'laydate', 'element' , 'upload'], function () {
            let $=layui.jquery,
                form = layui.form,
                table = layui.table,
                layer = layui.layer,
                common = layui.common,
                upload = layui.upload,
                music_list=[],
                play_list = document.querySelector("#play_list"),
                player = document.querySelector("#player"),
                play_index = 0;
            var fileTable = table.init('table', {
                page:false
            });
            var backward = function (){
                if(play_index==0){
                    play_index=music_list.length-1;
                }
                else{
                    play_index--;
                }
                //????????????
                loadMusic();
                //??????
                playMusic();
            };
            var forward = function (){
                if(play_index==music_list.length-1){
                    play_index=0;
                }
                else{
                    //??????????????????
                    play_index++;
                }
                //????????????
                loadMusic();
                //??????
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
                    //??????????????????????????????music???
                    var music = music_list[i];
                    //??????li??????
                    var liTag = document.createElement("li");
                    //???????????????span??????
                    var spanTitleTag = document.createElement("span");
                    //????????????span??????
                    var spanDurationTag = document.createElement("span");
                    //???ul??????li??????????????????
                    play_list.appendChild(liTag);
                    //???li????????????????????????
                    liTag.appendChild(spanTitleTag);
                    liTag.appendChild(spanDurationTag);

                    //????????????
                    spanTitleTag.innerHTML=music.name;
                    spanDurationTag.innerHTML='00:15';

                    //????????????
                    spanTitleTag.classList.add("list_title");
                    spanDurationTag.classList.add("list_time");

                    //???????????????
                    //????????????????????????????????????????????????????????????????????????
                    liTag.setAttribute("data-index",i.toString());

                    //??????????????????li???????????????
                    //????????????????????????(???????????????????????????????????????????????????)
                    //???????????????????????????
                    liTag.addEventListener("click",function(){
                        //????????????li???????????????id
                        play_index = this.getAttribute("data-index");

                        //?????????id?????????????????????play_index
                        loadMusic();
                        playMusic();

                    })
                    //????????????????????????
                    loadMusic();
                    //????????????
                    // player.muted = true;
                    // playMusic();
                }
            }

            var loadMusic = function (){
                var music = music_list[play_index];
                //???????????????
                list_title.innerHTML = music.name;
                //???????????????
                list_singer.innerHTML = 'unknown';
                //??????????????????
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
                    //??????????????????
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
                //paused,???????????????????????????????????????
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
                    {field:'id', title:'ID', minWidth:50}
                    ,{field:'name', title:'Name', minWidth:120 , edit:'text'}
                    ,{field:'hash', title:'Hash', minWidth:300}
                    ,{field:'time', title:'Time', minWidth:80 , edit:'text'}
                    ,{field:'status', title:'Status', minWidth:80 ,templet:function(d){
                            if(d.status==1)
                            {
                                return '<input type="checkbox" checked name="status" lay-skin="switch" lay-filter="switchAll" >';
                            }else{
                                return '<input type="checkbox"  name="status" lay-skin="switch" lay-filter="switchAll" >';
                            }
                        }}
                    ,{field:'recommendation', title:'Recommendation', minWidth:130 ,templet:function(d){
                            if(d.recommendation==1)
                            {
                                return '<input type="checkbox" checked name="recommendation" lay-skin="switch" lay-filter="switchAll" >';
                            }else{
                                return '<input type="checkbox"  name="recommendation" lay-skin="switch" lay-filter="switchAll" >';
                            }
                        }}
                    ,{field:'sort', title:'Sort', minWidth:80 , edit:'text'}
                    ,{field:'created_at', title:'CreatedAt', minWidth:180}
                    ,{field:'op', minWidth:120 , templet: '#operateTpl'}
                ]]
                ,page: true
                ,limit:4
                ,limits:[4]
                ,response: {
                    statusCode: 200
                }
                ,parseData: function(res){
                    console.log(res);
                    var list = [];
                    $(play_list).empty();
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

            //?????????????????????
            table.on('edit(music_table)', function(obj){
                var that = this;
                var value = obj.value //?????????????????????
                    ,data = obj.data //???????????????????????????
                    ,field = obj.field //????????????
                    ,original = $(this).prev().text(); //????????????
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('content::music.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
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
                    d[field] = original;
                    obj.update(d);
                    $(that).val(original);
                    table.render();
                });
            });

            table.on('tool(music_table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                var data = obj.data; //?????????????????????
                var layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                var tr = obj.tr; //??????????????? tr ???DOM??????

                if(layEvent === 'edit'){ //??????
                    //do somehing
                }else if(layEvent === 'del') { //??????
                    @if(!Auth::user()->can('content::music.destroy'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                    table.render();
                    return true;
                    @endif
                    common.confirm("{{trans('common.confirm.delete')}}" , function(){
                        common.ajax("{{url('/backstage/content/music')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete' , function (event,xhr,options,exc) {
                            setTimeout(function(){
                                common.init_error(event,xhr,options,exc);
                                form.render();
                            },100);
                        });
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });

            form.on('submit(form_submit_add)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined)
                    {
                        return true;
                    }
                    params[k] = v;
                });

                common.ajax("{{url('/backstage/content/music')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                        location.reload();
                    });
                });
                return false;
            });

            var music = upload.render({
                elem: '#music'
                ,accept: 'audio' //??????
                ,auto: false
                ,choose:function (obj , test){
                    var files = obj.pushFile();
                    // console.log(files);
                    var keys = Object.keys(files);
                    var end = keys[keys.length-1]
                    var file = files[end];
                    var formData = new FormData();
                    var jqureAjaxXhrOnProgress = function(fun) {
                        jqureAjaxXhrOnProgress.onprogress = fun; //????????????
                        //???????????????????????????
                        return function() {
                            //??????$.ajaxSettings.xhr();??????XMLHttpRequest??????
                            var xhr = $.ajaxSettings.xhr();
                            //?????????????????????????????????
                            if (typeof jqureAjaxXhrOnProgress.onprogress !== 'function')
                                return xhr;
                            //???????????????????????????xhr???????????????????????????????????????????????????
                            if (jqureAjaxXhrOnProgress.onprogress && xhr.upload) {
                                xhr.upload.onprogress = jqureAjaxXhrOnProgress.onprogress;
                            }
                            return xhr;
                        }
                    }
                    common.ajax("/backstage/qn/bundle/token" , {} , function(res){
                        formData.append('token', res.token);
                        formData.append('file',file);
                        $.ajax({
                            url:'https://up-z1.qiniup.com/',
                            type:'POST',
                            data: formData,
                            processData:false,
                            cache:false,
                            contentType:false,
                            xhr:jqureAjaxXhrOnProgress(function(e){
                                var percent=e.loaded / e.total;
                                percent = Math.round((percent + Number.EPSILON) * 100);
                                $("#upload_progress").html(percent);
                            }),
                            beforeSend: function(obj){ //obj??????????????????????????? choose???????????????????????????????????????
                                common.prompt('uploaded<span id="upload_progress"></span>%' , 16 , 0 , 0 , 'auto' , undefined , [0.8, '#393D49']);
                            },
                            success:function(data){
                                var fileListView = $('#fileList')
                                fileListView.find('.music-tr').remove();
                                var tr = $(['<tr class="music">'
                                    ,'<td><a href="'+res.domain+data.name+'" target="_blank">'+ res.domain+data.name +'</a>' +
                                    '<input type="hidden" name="music" value="'+res.domain+data.name+'"></td>'+
                                    '<input type="hidden" name="hash" value="'+data.hash+'"></td>'
                                    ,'</td>'
                                    ,'<td><button type="button" class="layui-btn layui-btn-xs layui-btn-danger file-delete">delete</button>'
                                    ,'</td>'
                                    ,'</tr>'].join(''));
                                //??????
                                var audio = new Audio(res.domain+data.name);
                                audio.load();
                                console.log(audio.readyState);
                                if(audio.readyState > 0)
                                {
                                    var minutes = parseInt(audio.duration / 60, 10);
                                    var seconds = parseInt(audio.duration % 60);
                                    console.log(minutes , seconds);
                                }
                                tr.find('.file-delete').on('click', function(obj){
                                    $(obj.target).closest('tr').remove();
                                    table.init('table', {
                                        page:false
                                    })
                                });
                                fileListView.append(tr);
                                browserMD5File(file, function (err, md5) {
                                    $("input[name=hash]").val(md5);
                                });
                                console.log(fileTable);
                                table.init('table', {
                                    page:false
                                })
                            },
                            error:function(){
                                alert('upload failed');
                            },
                            complete:function (){
                                layer.closeAll();
                            }
                        })
                    } , 'get' , undefined , undefined , false);
                }
            });

        });
    </script>
@endsection