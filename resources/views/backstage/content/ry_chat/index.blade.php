@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>消息记录</legend>
        </fieldset>
        <table class="layui-table"   lay-filter="chat_table" id="chat_table" >
            <thead>
            <tr>
                <th lay-data="{field:'chat_id', width:100}">消息ID</th>
                <th lay-data="{field:'chat_from_id', width:200}">消息发送用户ID</th>
                <th lay-data="{field:'chat_from_name', width:300}">消息发送用户账号</th>
                <th lay-data="{field:'chat_to_id', width:200}">消息接收用户ID</th>
                {{--<th lay-data="{field:'chat_content', minWidth:250 ,event:'showImage', templet:function(field){
                    if(field.chat_msg_type=='RC:ImgMsg')
                    {
                        return '<img src='+'\'data:image/jpeg;base64,'+field.chat_content+'\' >';
                    }else{
                        return field.chat_content;
                    }
                }}">消息内容</th>--}}
                <th  lay-data="{field:'chat_image', hide:true}"></th>
                <th lay-data="{field:'chat_msg_type', width:100}">消息类型</th>
                <th lay-data="{field:'chat_created_at', width:180}">消息日期</th>
            </tr>
            </thead>
            <tbody>
            @foreach($chats as $chat)
                <tr>
                    <td>{{$chat->chat_id}}</td>
                    <td>{{$chat->chat_from_id}}</td>
                    <td>{{urldecode($chat->chat_from_name)}}</td>
                    <td>{{$chat->chat_to_id}}</td>
{{--                    <td>{{$chat->chat_content}}</td>--}}
                    <td>{!! htmlspecialchars_decode($chat->chat_image) !!}</td>
                    <td>{{$chat->chat_msg_type}}</td>
                    <td>{{$chat->chat_created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $chats->links('vendor.pagination.default') }}
        @else
            {{ $chats->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                $=layui.jquery;
            table.init('chat_table', { //转化静态表格
                page:false,
                limit:{{$limit}},
                toolbar: '#toolbar'
            });

            table.on('tool(chat_table)', function(obj){
                var data = obj.data;
                console.log(data);


                if(obj.event === 'showImage'&&data.chat_msg_type=='RC:ImgMsg'){
                    var image = data.chat_image; //==''?"data:image/jpeg;base64," + data.chat_content:data.chat_image;
                    var  content = '<div></div>';
                    layer.photos({
                        photos: { "data": [{"src": $(content).html(image).text()}] }
                        ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机
                    });
                    // common.open(
                    //
                    //     // title: false,
                    //     // closeBtn: 0,
                    //     // area: ['auto'],
                    //     // skin: 'layui-layer-nobg', //没有背景色
                    //     // shadeClose: true,
                    //     content,
                    //     {
                    //         title:false,
                    //         resize:false,
                    //         scrollbar:false,
                    //         area:['auto']
                    //     },
                    //     1
                    // );
                }
            });

        });
    </script>

    <script type="text/html" id="toolbar">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">账号：</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="name" id="name" value="{{$name}}"/>
                </div>
                <button class="layui-btn" type="submit"  lay-submit >查询</button>
            </div>
        </form>
    </script>


@endsection