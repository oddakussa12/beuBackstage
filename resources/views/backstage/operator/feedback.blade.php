@extends('layouts.dashboard')
@section('layui-content')
    <style>td img { height: 30px; width: 30px; padding-right: 2px;}</style>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="fuzzy search" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn" type="submit" lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </div>
    </form>
    <table class="layui-table"   lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_name', width:150 ,fixed: 'left'}">UserName</th>
            <th  lay-data="{field:'user_avatar', width:80}">Avatar</th>
            <th  lay-data="{field:'user_nick_name', width:150}">UserNickName</th>
            <th  lay-data="{field:'image', width:150}">Image</th>
            <th  lay-data="{field:'content', minWidth:300}">Content</th>
            <th  lay-data="{field:'created_at', width:160}">CreateAT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->user_name}}</td>
                <td><img src="{{$l->user_avatar}}" /></td>
                <td>{{$l->user_nick_name}}</td>
                <td>@foreach($l->image as $image)
                    @if(!empty($image))<img src="{{$image}}" />@endif
                    @endforeach
                <td>{{$l->content}}</td>
                <td>{{$l->created_at}}</td>
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
        <!-- ?????????????????? -->
        ?? {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table', 'laydate'], function () {
            let $ = layui.jquery,
                table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                let data = obj.data; //?????????????????????
                let layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                let tr = obj.tr; //??????????????? tr ???DOM??????
                let id = data.id;

                if(layEvent === 'detail'){ //??????
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%','100%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/operator/operator/feedback'+id,
                    });
                }
            });
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar',
            });
            $(function () {
                let img_show = null; // tips??????
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
        })
    </script>
@endsection
