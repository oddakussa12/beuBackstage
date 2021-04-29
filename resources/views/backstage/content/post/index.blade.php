@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
         table td { height: 40px; line-height: 40px;}
        table td img { width: 60px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">UserId:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="keyword" placeholder="user_name" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$posts->total()}}</div>
                </div>
            </div>
        </form>
<!--        <div class="layui-btn-container">
{{--                        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>--}}
            <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>
            <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>
            <button class="layui-btn layui-btn-danger" lay-event="del">删除</button>
            <button class="layui-btn layui-btn-warm" lay-event="recover">取消删除</button>
        </div>-->
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
{{--                <th lay-data="{type:'checkbox', fixed: 'left'}"></th>--}}
                <th lay-data="{field:'post_id', minWidth:180}">PostId</th>
                <th lay-data="{field:'user_name', minWidth:160}">UserName</th>
                <th lay-data="{field:'image', minWidth:100}">Image</th>
                <th lay-data="{field:'video', minWidth:100,hide:'true'}">Video</th>
                <th lay-data="{field:'type', minWidth:100}">Type</th>
                <th lay-data="{field:'like', minWidth:100}">Likes</th>
                <th lay-data="{field:'comment', minWidth:100}">Comments</th>
                <th lay-data="{field:'audited', minWidth:100}">AuditState</th>
                <th lay-data="{field:'audited_at', minWidth:160}">AuditTime</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:200, align:'center', toolbar: '#postop'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($posts as $value)
                <tr>
{{--                    <td></td>--}}
                    <td>{{$value->post_id}}</td>
                    <td>{{$value->owner->user_name}}</td>
                    <td><img src="{{$value->image}}"></td>
                    <td>@if(!empty($value->video))<video style="height: 100%; width:100%" controls poster="{{$value->image}}"><source src="{{$value->video}}" type="video/mp4"></video>@endif</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->type=='image')layui-btn-warm @else layui-btn-normal @endif">{{$value->type}}</span></td>
                    <td>{{$value->like}}</td>
                    <td>{{$value->comment}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->audited==0)layui-btn-danger @elseif($value->audited==-1) layui-btn-warm @else  layui-btn-normal @endif">@if($value->audited==0)unaudited @elseif($value->audited==-1) refuse @else pass @endif</span></td>
                    <td>@if($value->audited_at!='0000-00-00 00:00:00') {{$value->audited_at}}@endif</td>
                    <td>{{$value->created_at}}</td>
                   <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $posts->links('vendor.pagination.default') }}
        @else
            {{ $posts->appends($appends)->links('vendor.pagination.default') }}
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
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.on('toolbar(table)', function(obj){
                const checkStatus = table.checkStatus(obj.config.id);
                let data = checkStatus.data;

                switch(obj.event){
                    case 'getCheckData':
                        data = checkStatus.data;
                        layer.alert(JSON.stringify(data));
                        break;
                    case 'getCheckLength':
                        data = checkStatus.data;
                        layer.msg('选中了：'+ data.length + ' 个');
                        break;
                    case 'isAll':
                        layer.msg(checkStatus.isAll ? '全选': '未全选');
                        break;
                    case 'del':
                        post(data, 'delete');
                        break;
                    case 'recover':
                        post(data, 'recover');
                        break;
                    //自定义头工具栏右侧图标 - 提示
                    case 'LAYTABLE_TIPS':
                        layer.alert('这是工具栏右侧自定义的一个图标按钮');
                        break;
                };
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                const data = obj.data; //获得当前行数据
                const layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'detail'){ //编辑
                    let video = $.trim(data.video);
                    let content = video==='' ? data.image : video;
                    let area = video==='' ? ['40%','95%'] : ['50%','50%'];
                    open(area, content, 1)
                }else if(layEvent === 'comment'){ //评论
                    open(['95%','95%'], '/backstage/content/post/'+data.post_id+'/comment')
                }else if(layEvent === 'delete') {
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/content/post')}}/"+data.post_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    });
                }
            });

            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                },
            });

            function post(data, type) {
                let arr = [];
                layer.confirm("{{trans('common.confirm.operate')}}", function(){
                    data.forEach(function (data) {
                        arr.push(data.post_id);
                    });
                    common.ajax("{{url('/backstage/content/post/batch')}}" , {"ids":arr, "type":type}, function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.operate')}}" , 1 , 500 , 6 , 't' ,function () {
                            console.log(res);
                            location.reload();
                        });
                    }, 'post');
                });
            }
            function open(area, content, types=2) {
                layer.open({
                    type: types,
                    shadeClose: true,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    content: content,
                });
            }
            $(function () {
                hoverOpenImg();
            });
            function  hoverOpenImg(){
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this,{
                        tips:1,
                    });
                },function(){
                    // layer.close(img_show);
                });
                //$('td img').attr('style','max-width:400px');
            }
        });

    </script>
    <script type="text/html" id="postop">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="comment">Comment</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection