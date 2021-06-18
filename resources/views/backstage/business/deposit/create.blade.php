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
                    <label class="layui-form-label">Money：</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="user_id" id="user_id" value="{{$id}}">
                        <input type="text" id="money" name="money" lay-verify="required|number" placeholder="only number"  required="required" autocomplete="off" class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Currency：</label>
                    <div class="layui-input-block">
                        <input type="text" id="currency" name="currency" required="required" autocomplete="off" class="layui-input" value="BIRR">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" lay-verify="required" id="dateTime" readonly required="required" name="dateTime" placeholder="{{trans('common.form.label.date')}}" value="@if(!empty($dateTime)){{$dateTime}}@endif">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <button class="layui-btn" lay-submit lay-filter="admin_form" id="btn">Submit</button>
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
        }).use(['common', 'table', 'layer', 'laydate', 'form'], function () {
            let form = layui.form,
                common = layui.common,
                $=layui.jquery,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                // ,max : 'today'
                ,type: 'datetime'
                ,lang: 'en'
            });
            form.on('submit(admin_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/business/deposits')}}", params, function(res){
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