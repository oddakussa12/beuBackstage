@extends('layouts.app')
@section('content')
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_id')}}：</label>
                    <div class="layui-input-inline">
                        <input type="text" id="user_id" name="user_id" placeholder="{{trans('user.form.placeholder.user_id')}}"  required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="common_form" id="btn">{{trans('common.form.button.add')}}</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@section('footerScripts')
    @parent
    <script type="text/javascript">
        layui.config({
            base: "{{url('plugin/layui')}}/",
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common', 'table', 'layer', 'form', 'upload', 'element'], function () {
            let form = layui.form,
                common = layui.common,
                $=layui.jquery;
            form.on('submit(common_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/passport/user/kol')}}", params, function(res){
                    parent.location.reload();
                } , 'post');
                return false;
            });
        });

    </script>
@endsection