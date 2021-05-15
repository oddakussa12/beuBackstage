@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">ShopName:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="shopName" placeholder="shop name" id="shopName" @if(!empty($shopName)) value="{{$shopName}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">GoodsName:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" placeholder="goods name" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Sort:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="created_at">CreatedAt</option>
                            <option value="like" @if(isset($sort) && $sort=='like') selected @endif>Liked</option>
                            <option value="price" @if(isset($sort) && $sort=='price') selected @endif>Price</option>
                            <option value="view_num" @if(isset($sort) && $sort=='view_num') selected @endif>ViewNum</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Recommend:</label>
                    <div class="layui-input-inline">
                        <select  name="recommend">
                            <option value="">All</option>
                            <option value="1" @if(isset($recommend) && $recommend=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($recommend) && $recommend=='0') selected @endif>NO</option>
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
                <th lay-data="{field:'chat_msg_uid', minWidth:180}">MessageId</th>
                <th lay-data="{field:'type', minWidth:180, hide:true}">Type</th>
                <th lay-data="{field:'audio', minWidth:180, hide:true}">Audio</th>

                <th lay-data="{field:'message_content', minWidth:180}">Content</th>
                <th lay-data="{field:'video_url', minWidth:180, hide:true}">VideoUrl</th>
                <th lay-data="{field:'chat_msg_type', minWidth:180}">Type</th>
                <th lay-data="{field:'created_at', minWidth:160}">ChatTime</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $key=>$value)
                <tr>
                    <td>{{$value->chat_msg_uid}}</td>
                    <td>{{$value->chat_msg_type}}</td>
                    <td>{{$value->message_content}}</td>
                    <td>
                        @if($value->chat_msg_type=='RC:ImgMsg') <img src="{{$value->message_content}}">
                        @elseif($value->chat_msg_type=='RC:TxtMsg') {{$value->message_content}}
                        @elseif($value->chat_msg_type=='Helloo:VoiceMsg')
                            @if($value->suffix=='wav')
                                <audio style="width:240px; height: 30px;" controls><source src="{{$value->message_content}}" type="audio/mpeg"></audio>
                            @else
                                <audio style="width:240px; height: 30px;" id="audio" controls><source src="{{$value->message_content}}" type="audio/mpeg"></audio>
                            @endif
                        @else
                        <video style="height: 100%; width:100%" controls><source src="{{$value->video_url}}" type="video/mp4"></video>
                        @endif
                    </td>
                    <td>{{$value->video_url}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->chat_msg_type=='RC:ImgMsg') layui-btn-danger
                    @elseif($value->chat_msg_type=='RC:TxtMsg') layui-btn-warm
                    @elseif($value->chat_msg_type=='Helloo:VoiceMsg') layui-btn-normal@else @endif">{{$value->chat_msg_type}} @if(!empty($value->suffix)){{$value->suffix}}@endif</span></td>
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
    <script src="/js/BenzAMRRecorder.js"></script>

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
                let area = ['40%','95%'];
                content = data.message_content;
                if (data.type==='Helloo:VoiceMsg') {
                    if (data.suffix==='wav') {
                        content = '<audio style="width:240px; height: 30px;" controls><source src="'+data.message_content+'" type="audio/mpeg"></audio>';
                    } else {
                        console.log(data.audio);
                        console.log(content);
                        $('#audio').attr('src', data.audio);
                        const amr = new BenzAMRRecorder();
                        amr.initWithUrl(data.audio).then(function () {
                            amr.play();
                        });
                        amr.onEnded(function () {
                            alert('播放完毕');
                        });
                    }
                } else if(data.type==='Helloo:VideoMsg'){
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
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">Detail</a>
    </script>
@endsection