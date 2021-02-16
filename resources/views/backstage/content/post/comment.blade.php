@extends('layouts.app')
@section('content')
    <style>
        .layui-carousel>[carousel-item]>* {display: block;}
        .layui-form-item .layui-inline {margin: 0}
    </style>
    <div  class="layui-row">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>{{trans('comment.html.comment_list')}}</legend>
        </fieldset>
        <table class="layui-table"   lay-filter="comment_table" id="comment_table" >

            <thead>
            <tr>
                <th lay-data="{field:'comment_id', width:100 ,fixed: 'left'}">{{trans('comment.table.header.comment_id')}}</th>
                <th lay-data="{field:'user_id', width:200,fixed: 'left'}">{{trans('comment.table.header.user_id')}}</th>
                <th lay-data="{field:'comment_to_id', width:200 ,fixed: 'left'}">{{trans('comment.table.header.comment_to_id')}}</th>
                <th lay-data="{field:'comment_content', width:400}">{{trans('comment.table.header.comment_content')}}</th>
                <th lay-data="{field:'comment_image', width:150}">{{trans('comment.table.header.comment_image')}}</th>
                <th lay-data="{field:'comment_created_at', width:200}">{{trans('comment.table.header.comment_create')}}</th>
                <th lay-data="{field:'comment_deleted', minWidth:120}">{{trans('comment.table.header.comment_delete')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($comments as $comment)
                <tr>
                    <td>{{$comment->comment_id}}</td>
                    <td>{{$comment->owner['user_name']}}</td>
                    <td>{{$comment->commentedOwner['user_name']}}</td>
                    <td>{{$comment->comment_content}}</td>
                    <td>
                        @if(!empty($comment->comment_image))
                        <div class="layui-carousel" >
                            <div carousel-item="">
                                @foreach($comment->comment_image as $image)
                                    <div><img src="//qnidyooulimage.mmantou.cn/{{$image}}?imageView2/0/w/50/h/50/interlace/1|imageslim"></div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </td>
                    <td>{{$comment->comment_created_at}}</td>
                    <td>
                        <input type="checkbox" name="delete" title="删除" @if(!empty($comment->comment_deleted_at)) checked="" @endif lay-filter="delete">
                    </td>
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
            table.on('tool(comment_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'check'){ //查看
                    //do somehing
                    alert('check');
                } else if(layEvent === 'delete'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/content/comment')}}/"+data.comment_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');
                    });
                } else if(layEvent === 'edit'){ //编辑
                    console.log(data);
                    var comment_id = data.comment_id;
                    layer.open({
                        type: 2,
                        title: '添加',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%','80%'],
                        offset: 'auto',
                        content: '/backstage/content/comment/'+comment_id+'/edit',
                    });
                    //do something
                    //同步更新缓存对应的值

                } else if(layEvent === 'comment'){ //编辑
                    var comment_id = data.comment_id;
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%','80%'],
                        offset: 'auto',
                        content: '/backstage/content/comment/'+comment_id+'/comment',
                    });
                    //do something
                    //同步更新缓存对应的值

                } else if(layEvent === 'unpopular'){ //热门

                }
            });
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var commentId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('content::comment.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                var name = $(data.elem).attr('name');
                if(checked)
                {
                    var params = '{"'+name+'":"on"}';
                }else{
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/content/comment')}}/"+commentId , JSON.parse(params) , function(res){
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

            form.on('checkbox(delete)', function(obj){
                var checked = obj.elem.checked;
                console.log(obj.elem.checked);
                var commentId = obj.othis.parents('tr').find("td :first").text();
                obj.elem.checked = !checked;
                @if(!Auth::user()->can('content::postComment.destroy'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", obj.othis);
                form.render();
                return false;
                        @endif
                form.render();
                var name = $(obj.elem).attr('name');

                layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                    common.ajax("{{url('/backstage/content/postComment')}}/"+commentId , {} , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                            // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            // layer.close(index);
                            location.reload();
                        });
                    } , 'delete');
                });
                // layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
            });
            table.init('comment_table', { //转化静态表格
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
    <script type="text/html" id="toolbar">
        <form class="layui-form">
            <div class="layui-form-item">

                <div class="layui-input-inline" style="width: 350px">
                    <label class="layui-form-label">UUID</label>
                    <input class="layui-input" style="width: 270px; float: left;" name="post_uuid" id="post_uuid" @if(!empty($post_uuid)) value="{{$post_uuid}}" @endif />
                </div>
                <div class="layui-input-inline" style="width: 240px">
                    <label class="layui-form-label">{{trans('comment.form.label.user_name')}}:</label>
                    <input class="layui-input" style="width: 150px;float: left;" name="user_name" id="user_name" @if(!empty($user_name)) value="{{$user_name}}" @endif />
                </div>
                <div class="layui-inline" >
                    <div class="layui-input-inline" style="width: 400px;">
                        <label class="layui-form-label">{{trans('comment.form.label.comment_created_at')}}:</label>
                        <input type="text" class="layui-input" style="width: 300px;" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>

            </div>

            {{--<button class="layui-btn layui-btn-sm" id="create">{{trans('common.table.tool.button.create')}}</button>--}}
        </form>
    </script>
    <script type="text/html" id="commentop">
        {{--<a class="layui-btn layui-btn-xs" lay-event="check">{{trans('common.table.button.check')}}</a>--}}
{{--        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>--}}

        {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>--}}
    </script>
@endsection