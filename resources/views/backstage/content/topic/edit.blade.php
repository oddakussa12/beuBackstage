@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}

<div class="layui-container">

    <form class="layui-form layui-tab-content">
        {{ csrf_field() }}
        <div class="layui-form-item">
            <label class="layui-form-label">名称：</label>
            <div class="layui-input-block">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id" value="{{ $data->id }}"/>
                <input type="text" name="topic_content" autocomplete="off" required="required" placeholder="话题名称" class="layui-input" value="{{ $data->topic_content }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">类型：</label>
            <div class="layui-input-block">
                <select name="flag">
                    <option value="1" @if ($data->flag==1) selected @endif>官方话题</option>
                    <option value="2" @if ($data->flag==2) selected @endif>热门话题</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序：</label>
            <div class="layui-input-block">
                <input type="text" name="sort" autocomplete="off" required="required" placeholder="数值越大，越靠前" class="layui-input" value="{{ $data->sort }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开始时间：</label>
            <div class="layui-input-block">
                <input type="text" id="start_time" name="start_time" required="required" autocomplete="off" class="layui-input" value="@if ($data->start_time){{date('Y-m-d H:i:s', $data->start_time)}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结束时间：</label>
            <div class="layui-input-block">
                <input type="text" id="end_time" name="end_time" required="required" autocomplete="off" class="layui-input" value="@if ($data->end_time){{date('Y-m-d H:i:s', $data->end_time)}}@endif">
            </div>
        </div>
{{--        <div class="layui-form-item">--}}
{{--            <label class="layui-form-label">当前状态：</label>--}}
{{--            <div class="layui-input-block">--}}
{{--                <select name="is_delete">--}}
{{--                    <option value="1" @if ($data->is_delete==1) selected @endif>已删除</option>--}}
{{--                    <option value="0" @if ($data->is_delete!=1) selected @endif>状态正常</option>--}}
{{--                </select>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">提交</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footerScripts')
@parent
<script type="text/html" id="operateTpl">
    <div class="layui-table-cell laytable-cell-1-6">
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
    </div>
</script>

<script>
    layui.config({
        base: "{{url('plugin/layui')}}/"
    }).extend({
        common: 'lay/modules/admin/common',
        formSelects: 'lay/modules/formSelects-v4',
    }).use(['common' , 'tree' , 'table' , 'layer', 'laydate', 'formSelects'], function () {
        var $ = layui.jquery,
            laydate = layui.laydate,
            table = layui.table,
            form = layui.form,
            tree = layui.tree,
            common = layui.common,
            layer = layui.layer;
        formSelects = layui.formSelects;
        //formSelects.render('admin_roles');
        //执行一个laydate实例
        laydate.render({
            elem: '#start_time', //指定元素
            type: 'datetime',
            calendar: true
        });
        laydate.render({
            elem: '#end_time', //指定元素
            type: 'datetime',
            calendar: true
        });
        form.on('submit(admin_form)', function(data){
            let params = {};
            $.each(data.field , function (k ,v) {
                if(v==''||v==undefined) {return true;}
                params[k] = v;
            });
            console.log(params);
            console.log('ajax start');
            common.ajax("{{url('/backstage/content/topic/')}}/"+params.id , params , function(res){
                parent.location.reload();
            } , 'patch');
            console.log('end');
            return false;
        });

    });
</script>
<style>
    .multi dl dd.layui-this{background-color:#fff}
</style>
@endsection
