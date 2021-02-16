@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <style>
        .layui-input-block select {display: block;}
    </style>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-card-header">热门话题</div>
            <div class="layui-card-body">
                <form class="layui-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="topic_content" autocomplete="off"placeholder="话题名称" class="layui-input" value="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">类型：</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select name="field">
                                    <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                                        <option value="123" >}}</option>
                                </select>
                            </div>
                            <select>
                                <option value="1">官方话题</option>
                                <option value="2">热门话题</option>
                                <option value="0">其他</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">排序：</label>
                        <div class="layui-input-block">
                            <input type="text" name="sort" autocomplete="off" placeholder="数值越大，越靠前" class="layui-input" value="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开始时间：</label>
                        <div class="layui-input-block">
                            <input type="text" name="start_time" autocomplete="off" class="layui-input" value="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">结束时间：</label>
                        <div class="layui-input-block">
                            <input type="text" name="end_time" autocomplete="off" class="layui-input" value="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="config_submit_btn">保存</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            formSelects: 'lay/modules/formSelects-v4',
        }).use(['common' , 'tree' , 'table' , 'layer' , 'formSelects'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                tree = layui.tree,
                common = layui.common,
                layer = layui.layer;
            var formSelects = layui.formSelects;
        })
    </script>
