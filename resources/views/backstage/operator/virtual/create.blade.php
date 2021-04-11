@extends('layouts.app')
@section('content')
    @php
        $qn_token = qnToken('qn_event_source');
    @endphp
    <div class="layui-fluid">
        <form class="layui-form layui-tab-content">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">ID：</label>
                    <div class="layui-input-block">
                        <input type="text" id="user_name" name="user_name" placeholder="User ID"  required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="admin_form" id="btn">Submit</button>
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
            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/operator/virtual')}}", params, function(res){
                    console.log(res);
                    console.log(res.code);
                    if (res.code!== undefined) {
                        layer.open({
                            title: 'Result'
                            ,content: res.message
                        });
                    }
                    parent.location.reload();
                } , 'post');
                return false;
            });
        });

    </script>
@endsection