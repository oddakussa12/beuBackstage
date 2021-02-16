@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    {{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <fieldset class="layui-elem-field layui-field-title">
        <legend></legend>
    </fieldset>
    <div class="layui-container">
        <style>
            .layui-layout-body {overflow: auto;}
            .layui-form-label {padding:9px; width: 112px;}
            .layui-input {width: 200px;}
            .layui-input-block {width:200px; padding-left:20px;}
        </style>
        <form class="layui-form layui-tab-content" method="post" action="/backstage/invitation/activity">
            {{ csrf_field() }}

            <div class="layui-form-item">
                <label class="layui-form-label">请选择国家：</label>
                <div class="layui-input-inline layui-form-select">
                    <select  name="country" lay-verify="" lay-search>
                        <option value="">{{trans('user.form.placeholder.user_country_id')}}</option>
                        @foreach($counties  as $country)
                            <option value="{{$country['code']}}" @if($data['country']==$country['code']) selected @endif>{{$country['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="float: none;"></div>
            <div class="layui-form-item">
                <div style="color:red">邀请活动设置：</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">活动状态：</label>
                <div class="layui-input-inline">
                    <select  name="activity_status" lay-verify="">
                        <option value="0">关闭</option>
                        <option value="1">开启</option>
                    </select>
                </div>
                <label style="width: 125px;" class="layui-form-label">有效期：</label>
                <div class="layui-input-inline">
                    <select  name="activity_expire" lay-verify="">
                        <option value="0" >不限</option>
                        <option value="1">自定义</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">开始时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="activity_start_time" name="activity_start_time" placeholder="开始时间"  autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结束时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="activity_end_time" name="activity_end_time" placeholder="结束时间" autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div style="color:red">积分兑换设置：</div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">兑换状态：</label>
                    <div class="layui-input-inline">
                        <select  name="order_status" lay-verify="" lay-search>
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">兑换有效期：</label>
                    <div class="layui-input-inline">
                        <select  name="order_expire" lay-verify="">
                            <option value="0">不限</option>
                            <option value="1">自定义</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class=" layui-form-label">开始时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="order_start_time" name="order_start_time" placeholder="开始时间" autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结束时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="order_end_time" name="order_end_time" placeholder="结束时间" autocomplete="off" class="layui-input time" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div style="color:red">积分发放规则：每邀请一人，积分增加额度：</div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">首次注册：</label>
                    <div class="layui-input-block">
                        <input type="text" id="first_register" name="first_register" required="required" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">连续两天：</label>
                    <div class="layui-input-block">
                        <input type="text" id="second" name="second" required="required" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">连续七天：</label>
                    <div class="layui-input-block">
                        <input type="text" id="seven" name="seven" required="required" class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">连续三十天：</label>
                    <div class="layui-input-block">
                        <input type="text" id="thirty" name="thirty" required="required" class="layui-input" value="">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">提交</button>
            </div>
        </form>
    </div>
@endsection

@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
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
            lay('.time').each(function(){
                laydate.render({
                    elem: this, //指定元素
                    type: 'datetime',
                    calendar: true
                });
            });

            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined) {return true;}
                    params[k] = v;
                });
                console.log('1111111111111111111111');
                console.log(params);
                console.log(2222222222222222222222);

                console.log('ajax start');
                common.ajax("{{url('/backstage/invitation/activity/')}}" , params , function(res){
                    // console.log(res);
                   parent.location.reload();
                } , 'post');
                console.log('end');
                return false;
            });

        });
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
