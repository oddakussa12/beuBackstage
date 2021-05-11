@extends('layouts.app')
@section('content')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
    </style>
    <br>
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">UserId:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" placeholder="user_name" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
            </div>
            <div class="layui-inline">
                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </div>
    </form>
    <div  class="layui-row">
        <table class="layui-table" lay-filter="table" id="table" >
            <thead>
            <tr>
                <th lay-data="{field:'comment_id', minWidth:180 ,fixed: 'left'}">CommentId</th>
                <th lay-data="{field:'user_avatar', width:80}">Avatar</th>
                <th lay-data="{field:'user_nick_name', minWidth:160}">NickName</th>
                <th lay-data="{field:'content', minWidth:300}">Content</th>
                <th lay-data="{field:'level', minWidth:100}">Level</th>
                <th lay-data="{field:'child_comment', minWidth:110}">ChildComment</th>
                <th lay-data="{field:'created_at', width:160}">CreatedAt</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($comments as $comment)
                <tr>
                    <td>{{$comment->comment_id}}</td>
                    <td><img src="{{$comment->owner['user_avatar']}}" /></td>
                    <td>{{$comment->owner['user_nick_name']}}</td>
                    <td>{{$comment->content}}</td>
                    <td>{{$comment->level}}</td>
                    <td>{{$comment->child_comment}}</td>
                    <td>{{$comment->created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $comments->links('vendor.pagination.default') }}
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
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'carousel' , 'timePicker'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                carousel = layui.carousel,
                timePicker = layui.timePicker,
                $=layui.jquery;
            carousel.render({
                elem: '.layui-carousel'
                ,width: '100px'
                ,height: '100px'
                ,interval: 1000
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'check'){ //查看
                    //do somehing
                    alert('check');
                } else if(layEvent === 'delete'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/content/post/comment')}}/"+data.comment_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                               location.reload();
                            });
                        } , 'delete');
                    });
                }
            });
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar',
                height: 515 //固定值
            });

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection