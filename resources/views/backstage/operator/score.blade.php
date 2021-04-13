@extends('layouts.dashboard')
@section('layui-content')
    <style>
        table td img { height: 40px; }
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
                <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                <div class="layui-input-inline">
                    <select name="sort" lay-verify="">
                        @foreach($type as $item)
                            <option value="{{$item}}" @if(!empty($sort)&&$sort==$item) selected @endif>{{str_replace(' ', '', ucwords(str_replace('_', ' ', $item)))}}</option>
                        @endforeach
                    </select>
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
            <th  lay-data="{field:'user_id', minWidth:110 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'user_avatar', minWidth:100}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_name', minWidth:140}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_nickname', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'init', minWidth:100}">InitScore</th>
            <th  lay-data="{field:'score', minWidth:120}">TotalScore</th>
            <th  lay-data="{field:'sent', minWidth:100}">Sent</th>
            <th  lay-data="{field:'friend', minWidth:80}">Friend</th>
            <th  lay-data="{field:'like', minWidth:80}">Like</th>
            <th  lay-data="{field:'liked', minWidth:80}">Liked</th>
            <th  lay-data="{field:'video', minWidth:80}">Video</th>
            <th  lay-data="{field:'txt', minWidth:80}">Txt</th>
            <th  lay-data="{field:'audio', minWidth:80}">Audio</th>
            <th  lay-data="{field:'image', minWidth:80}">Image</th>
            <th  lay-data="{field:'props', minWidth:80}">Props</th>
            <th  lay-data="{field:'like_video', minWidth:120}">LikeVideo</th>
            <th  lay-data="{field:'liked_video', minWidth:120}">LikedVideo</th>
            <th  lay-data="{field:'game_score', minWidth:120}">GameScore</th>
            <th  lay-data="{field:'other_school_friend', minWidth:160}">OtherSchoolFriend</th>
            <th  lay-data="{field:'created_at', minWidth:160}">{{trans('user.table.header.user_time')}}</th>
            <th lay-data="{fixed: 'right', minWidth:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->user_id}}</td>
                <td>@if(isset($l->user_avatar))<img src="{{$l->user_avatar}}" />@endif</td>
                <td>@if(isset($l->user_name)){{$l->user_name}}@else 0 @endif</td>
                <td>@if(isset($l->user_nick_name)){{$l->user_nick_name}}@else 0 @endif</td>
                <td>@if(isset($l->init)){{$l->init}}@else 0 @endif</td>
                <td>@if(isset($l->score)){{$l->score}}@else 0 @endif</td>
                <td>@if(isset($l->sent)){{$l->sent}}@else 0 @endif</td>
                <td>@if(isset($l->friend)){{$l->friend}}@else 0 @endif</td>
                <td>@if(isset($l->like)){{$l->like}}@else 0 @endif</td>
                <td>@if(isset($l->liked)){{$l->liked}}@else 0 @endif</td>
                <td>@if(isset($l->video)){{$l->video}}@else 0 @endif</td>
                <td>@if(isset($l->txt)){{$l->txt}}@else 0 @endif</td>
                <td>@if(isset($l->audio)){{$l->audio}}@else 0 @endif</td>
                <td>@if(isset($l->image)){{$l->image}}@else 0 @endif</td>
                <td>@if(isset($l->props)){{$l->props}}@else 0 @endif</td>
                <td>@if(isset($l->like_video)){{$l->like_video}}@else 0 @endif</td>
                <td>@if(isset($l->liked_video)){{$l->liked_video}}@else 0 @endif</td>
                <td>@if(isset($l->game_score)){{$l->game_score}}@else 0 @endif</td>
                <td>@if(isset($l->other_school_friend)){{$l->other_school_friend}}@else 0 @endif</td>
                <td>@if(isset($l->created_at)){{$l->created_at}}@endif</td>
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
    </script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table', 'common', 'laydate'], function () {
            let $ = layui.jquery,
                table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });
            table.init('table', {
                page:false
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'detail'){
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%','80%'],
                        offset: 'auto',
                        scrollbar:true,
                        content: '/backstage/operator/operator/score/detail/'+data.user_id,
                    });
                }
            });
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
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
