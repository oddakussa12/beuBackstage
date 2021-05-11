@extends('layouts.dashboard')
@section('layui-content')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { height: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="fuzzy search" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('common.form.label.type')}}:</label>
                <div class="layui-input-inline">
                    <select id="media" name="media" lay-verify="">
                        @foreach($type as $item)
                            <option value="{{$item}}" @if(!empty($media)&&$media==$item) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder="yyyy-MM-dd - yyyy-MM-dd" @if(!empty($dateTime))value="{{$dateTime}}"@endif>
                </div>
            </div>

            <div class="layui-inline">
                <button class="layui-btn" type="submit" lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </div>
    </form>
    <table class="layui-table" lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'id', minWidth:140 ,fixed: 'left', hide: 'true'}">MediaID</th>
            <th  lay-data="{field:'user_id', minWidth:140 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'user_avatar', minWidth:150}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_name', minWidth:160}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_nickname', minWidth:190}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'image', minWidth:160}">Image</th>
            <th  lay-data="{field:'video_url', width:160}">Video</th>
            <th  lay-data="{field:'created_at', width:200}">{{trans('user.table.header.user_time')}}</th>
            <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>@if(!empty($l->photo_id)){{$l->photo_id}}@else{{$l->video_id}}@endif</td>
                <td>{{$l->user_id}}</td>
                <td><img src="{{$l->user_avatar}}" /></td>
                <td>{{$l->user_name}}</td>
                <td>{{$l->user_nick_name}}</td>
                <td><img  src="{{$l->image}}"/></td>
                <td>@if(!empty($l->video_url))<video style="height: 100%; width:100%" controls poster="{{$l->image}}"><source src="{{$l->video_url}}" type="video/mp4"></video>@endif</td>
                <td>{{$l->created_at}}</td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $list->links('vendor.pagination.default') }}
    @else
        {{ $list->appends($appends)->links('vendor.pagination.default') }}
    @endif
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
{{--        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">{{trans('common.table.button.delete')}}</a>--}}
    </script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table', 'common', 'laydate'], function () {
            let $ = layui.jquery,
                common = layui.common,
                table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let video = $.trim(data.video_url);
                let content = video==='' ? data.image : video;
                let area = video==='' ? ['40%','95%'] : ['50%','50%'];
                if(layEvent === 'detail'){
                    layer.open({
                        type: 1,
                        shadeClose: true,
                        shade: 0.8,
                        area: area,
                        offset: 'auto',
                        skin: 'layer-alert-video',
                        scrollbar:true,
                        content: content
                    });
                } else if(layEvent === 'del'){ //删除
                    data.type = $('#media').val();
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/operator/operator/media/destroy')}}", data , function(res){
                            console.log(res);
                            let prompt = res.code===0 ? "{{trans('common.ajax.result.prompt.delete')}}" : "Operation failed, please try again";
                            common.prompt(prompt, 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'post');
                        //向服务端发送删除指令
                    });
                }
            });
            table.init('table', {
                page:false
            });
            $(function () {
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
        })
    </script>
@endsection
