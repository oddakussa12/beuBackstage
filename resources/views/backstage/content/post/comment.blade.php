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
        //??????????????????
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
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                var data = obj.data; //?????????????????????
                var layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                var tr = obj.tr; //??????????????? tr ???DOM??????

                if(layEvent === 'check'){ //??????
                    //do somehing
                    alert('check');
                } else if(layEvent === 'delete'){ //??????
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/content/post/comment')}}/"+data.comment_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //??????????????????tr??????DOM????????????????????????
                                // layer.close(index);
                               location.reload();
                            });
                        } , 'delete');
                    });
                }
            });
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar',
                height: 515 //?????????
            });

            timePicker.render({
                elem: '#dateTime', //???????????????input??????
                options:{      //????????????timeStamp???format
                    timeStamp:false,//true??????????????? ?????????format?????????????????????false??????????????? //??????false
                    format:'YYYY-MM-DD HH:ss:mm',//?????????????????????????????????moment.js?????? ?????????YYYY-MM-DD HH:ss:mm
                    locale:"{{locale()}}"
                },
            });

        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection