@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; max-width: 30px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
        .audio {display: block;width: 40px;float: left; color: #FFFFFF;text-align: center; border-radius: 15px; position: absolute; z-index: 1; left: 5px;}
        .layui-layer-content { display: flex; align-items: center; justify-content: center; text-align: justify; margin:0 auto; }
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('chat.table.header.chat_mode')}}:</label>
                    <div class="layui-input-inline">
                        <select name="mode">
                            <option value="">BothWay</option>
                            <option value="1" @if(!empty($mode)) selected @endif>OneWay</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('chat.table.header.from_user')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="sender" placeholder="user name" id="sender" @if(!empty($sender)) value="{{$sender}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('chat.table.header.to_user')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="received_by" placeholder="user name" id="received_by" @if(!empty($received_by)) value="{{$received_by}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.type')}}:</label>
                    <div class="layui-input-inline">
                        <select name="type">
                            <option value="">ALL</option>
                            <option value="shop" @if(isset($type) && $type=='shop') selected @endif>Shop</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select name="sort">
                            <option value="DESC" @if(isset($sort) && $sort=='DESC') selected @endif>DESC</option>
                            <option value="ASC"  @if(isset($sort) && $sort=='ASC')  selected @endif>ASC</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>

        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'chat_msg_uid', minWidth:200}">{{trans('chat.table.header.message_id')}}</th>
                <th lay-data="{field:'chat_msg_type', minWidth:160}">{{trans('chat.table.header.type')}}</th>
                <th lay-data="{field:'type', minWidth:180, hide:true}">{{trans('chat.table.header.type')}}</th>
                <th lay-data="{field:'audio', minWidth:180, hide:true}">Audio</th>
                <th lay-data="{field:'from_name', minWidth:180}">{{trans('chat.table.header.from_user')}}</th>
                <th lay-data="{field:'to_name', minWidth:180}">{{trans('chat.table.header.to_user')}}</th>
                <th lay-data="{field:'message_content', minWidth:300}">{{trans('chat.table.header.content')}}</th>
                <th lay-data="{field:'video_url', minWidth:180, hide:true}">VideoUrl</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('chat.table.header.chat_time')}}</th>
                <th lay-data="{fixed: 'right', width:80, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $key=>$value)
                <tr>
                    <td>{{$value->chat_msg_uid}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->chat_msg_type=='RC:ImgMsg') layui-btn-danger
                    @elseif($value->chat_msg_type=='RC:TxtMsg') layui-btn-warm
                    @elseif($value->chat_msg_type=='Helloo:VoiceMsg') layui-btn-normal
                    @elseif($value->chat_msg_type=='Helloo:GoodsMsg') layui-btn-checked
                    @else @endif">{{$value->chat_msg_type}} @if(!empty($value->suffix)){{$value->suffix}}@endif</span></td>
                    <td>{{$value->chat_msg_type}}</td>
                    <td>{{$value->message_content}}</td>
                    <td>@if(!empty($value->from))
                            @if(!empty($value->chat_from_type)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/shop')}}?keyword={{$value->from['user_nick_name']}}">{{$value->from['user_nick_name']}}</a>@else {{$value->from['user_nick_name']}} @endif
                        @endif
                    </td>
                    <td>@if(!empty($value->to))
                            @if(!empty($value->chat_to_type)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/shop')}}?keyword={{$value->to['user_nick_name']}}">{{$value->to['user_nick_name']}}</a>@else {{$value->to['user_nick_name']}} @endif
                        @endif</td>
                    <td>
                        @if($value->chat_msg_type=='RC:ImgMsg') <img src="{{$value->message_content}}">
                        @elseif($value->chat_msg_type=='RC:TxtMsg') {!! $value->message_content !!}
                        @elseif($value->chat_msg_type=='Helloo:GoodsMsg')
                            <a target="_blank" style="color: #5FB878" href="{{url('/backstage/business/goods')}}?goods_id={{$value->message_content}}">{{$value->message_content}}</a>
                        @elseif($value->chat_msg_type=='Helloo:VoiceMsg')
                            @if($value->suffix=='wav')
                                <audio style="width:240px; height: 30px;" controls><source src="{{$value->message_content}}" type="audio/mpeg"></audio>
                            @else
                                <a onclick=playAudio("{{$value->message_content}}") href="javascript:;" class="audio layui-btn-danger">Play</a>
                                <audio style="width:180px; height: 30px; margin-left: -10px" controls><source src="{{$value->message_content}}" type="audio/mpeg"></audio>
                            @endif
                        @else
                            @if(!empty($value->video_url))<video style="height: 100%; width:100%" controls><source src="{{$value->video_url}}" type="video/mp4"></video>@endif
                        @endif
                    </td>
                    <td>@if(!empty($value->video_url)){{$value->video_url}}@endif</td>
                    <td>{{$value->chat_created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $result->links('vendor.pagination.default') }}
        @else
            {{ $result->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
    <script src="/js/amrwb-js/amrwb.js" defer></script>
    <script src="/js/amrwb-js/amrwb-util.js" defer></script>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'laydate', 'timePicker'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                $ = layui.jquery,
                laydate = layui.laydate;

            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            laydate.render({
               elem: "#dateTime",
               type: 'month',
                lang:'en'
            });

            form.on('switch(switchAll)', function(data){
                let params;
                const checked = data.elem.checked;
                const id = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('business::goods.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                const name = $(data.elem).attr('name');
                if(checked) {
                    params = '{"' + name + '":"on"}';
                }else {
                    params = '{"' + name + '":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/goods')}}/"+id , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let content;
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let area = data.type==='RC:ImgMsg' ? ['95%','95%'] : ['40%','30%'];
                content = data.message_content;
                if (data.type==='Helloo:VoiceMsg') {
                    if (data.suffix==='wav') {
                        content = '<audio style="width:240px; height: 30px;" controls><source src="'+data.message_content+'" type="audio/mpeg"></audio>';
                    }
                }
                if(data.type==='Helloo:VideoMsg'){
                    area = ['50%','60%'];
                    content = '<video controls="controls" autoplay="autoplay" width="100%" height="380px"><source src="'+data.video_url+'" type="video/mp4" /></video>';
                }
                if(layEvent === 'detail'){
                    layer.open({
                        type: 1,
                        shadeClose: true,
                        shade: 0.8,
                        area: area,
                        offset: 'auto',
                        scrollbar:true,
                        content: content,
                    });
                }
            });
            $(function () {
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
        });


        function playAudio(file) {
            file = '/js/amrwb-js/audio/233752199224623104.amr';
            fetchBlob(file, function(blob) {
                playAmrBlob(blob);
            });
        }

        let gAudioContext = new AudioContext();

        function getAudioContext() {
            if (!gAudioContext) {
                gAudioContext = new AudioContext();
            }
            return gAudioContext;
        }

        function fetchBlob(url, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.onload = function() {
                callback(this.response);
            };
            xhr.onerror = function() {
                alert('Failed to fetch ' + url);
            };
            xhr.send();
        }

        function readBlob(blob, callback) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                callback(data);
            };
            reader.readAsArrayBuffer(blob);
        }

        function playAmrBlob(blob, callback) {
            readBlob(blob, function(data) {
                playAmrArray(data);
            });
        }

        function playAmrArray(array) {
            AMRWB.decodeInit();
            const samples = AMRWB.decode(array);
            AMRWB.decodeExit();
            if (!samples) {
                alert('Failed to decode!');
                return;
            }
            playPcm(samples);
        }

        function playPcm(samples) {
            const ctx = getAudioContext();
            const src = ctx.createBufferSource();
            const buffer = ctx.createBuffer(1, samples.length, 16000);
            if (buffer.copyToChannel) {
                buffer.copyToChannel(samples, 0, 0)
            } else {
                const channelBuffer = buffer.getChannelData(0);
                channelBuffer.set(samples);
            }
            src.buffer = buffer;
            src.connect(ctx.destination);
            src.start();
        }
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
    </script>
@endsection