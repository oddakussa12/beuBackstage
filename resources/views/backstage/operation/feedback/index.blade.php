@extends('layouts.dashboard')
@section('layui-content')
    <style>td img { height: 30px; width: 30px; padding-right: 2px;}</style>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="{{trans('user.form.placeholder.user_name')}}" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
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
            <th  lay-data="{field:'user_name', width:150 ,fixed: 'left'}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_nick_name', width:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'image', width:150}">{{trans('operation.table.header.feedback.image')}}</th>
            <th  lay-data="{field:'content', minWidth:300}">{{trans('operation.table.header.feedback.content')}}</th>
            <th  lay-data="{field:'created_at', width:160}">{{trans('common.table.header.created_at')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($feedbacks as $l)
            <tr>
                <td>{{$l->user_name}}</td>
                <td><img src="{{splitJointQnImageUrl($l->user_avatar)}}" /></td>
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
        {{ $feedbacks->links('vendor.pagination.default') }}
    @else
        {{ $feedbacks->appends($appends)->links('vendor.pagination.default') }}
    @endif

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table'], function () {
            let $ = layui.jquery,
                table = layui.table;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar',
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
