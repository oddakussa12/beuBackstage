@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-select .layui-input{ padding-right: 0}
    </style>
    <div class="layui-container">
        <form class="layui-form layui-tab-content"  lay-filter="admin_form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-block">
                        <input type="hidden" name="admin_id" value="{{$user->admin_id}}" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_email')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_email" disabled lay-verify="admin_email" class="layui-input" value="{{$user->admin_email}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_username')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_username"  placeholder="{{trans('admin.form.placeholder.admin_username')}}" lay-verify="admin_username" autocomplete="off" class="layui-input" value="{{$user->admin_username}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_realname')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_realname"  placeholder="{{trans('admin.form.placeholder.admin_realname')}}" lay-verify="admin_realname" autocomplete="off" class="layui-input" value="{{$user->admin_realname}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_sex')}}</label>
                    <div class="layui-input-block">
                        <select name="admin_sex" >
                            <option value="">-{{trans('common.form.placeholder.select_first')}}-</option>
                            @php
                                $sex_list = config('common.sex');
                                krsort($sex_list);
                            @endphp
                            @foreach($sex_list as $k=>$sex)
                                <option id="sex_class_id_{{$k}}" @if($user->admin_sex==$k) selected @endif value="{{$k}}" >{{trans($sex)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.old_password')}}</label>
                    <div class="layui-input-block">
                        <input type="password" name="password"  placeholder="{{trans('admin.form.label.old_password')}}" lay-verify="password" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.new_password')}}</label>
                    <div class="layui-input-block">
                        <input type="password" name="new_password"  placeholder="{{trans('admin.form.label.new_password')}}" lay-verify="admin_realname" autocomplete="off" class="layui-input" >
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.confirm_password')}}</label>
                    <div class="layui-input-block">
                        <input type="password" name="confirm_password"  placeholder="{{trans('admin.form.label.confirm_password')}}" lay-verify="admin_realname" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="form_submit_update">{{trans('config.common.button.submit')}}</button>
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
        }).use(['common' , 'form'], function () {
            var $ = layui.jquery,
            form = layui.form,
            common = layui.common;
            form.on('submit(form_submit_update)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if (v==''||v==undefined) {
                        return true;
                    }
                    params[k] = v;
                });
                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/admin/self')}}", params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'patch');
            });
            form.on('submit(admin_form)', function(){
                return false;
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
