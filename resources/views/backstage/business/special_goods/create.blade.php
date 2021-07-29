@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-fluid">
        <form class="layui-form">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods.id')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="goods_id" lay-verify="required" required placeholder="{{trans('business.form.placeholder.goods.id')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.special_price')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="special_price" lay-verify="required" required placeholder="{{trans('business.form.placeholder.special_goods.special_price')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.packaging_cost')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="packaging_cost" lay-verify="required" required placeholder="{{trans('business.form.placeholder.special_goods.packaging_cost')}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.free_delivery')}}：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.deadline')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="date" required placeholder="{{trans('common.form.label.date')}}" id="deadline" name="deadline">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" type="submit"  lay-submit lay-filter="common_form" >{{trans('common.form.button.add')}}</button>
                    </div>
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
        </div>
    </script>
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common', 'table', 'layer', 'laydate'], function () {
            var form = layui.form,
                laydate = layui.laydate,
                common = layui.common,
                $=layui.jquery;
            laydate.render({
                elem: '#deadline'
                ,min : 'today'
                ,type: 'datetime'
                ,lang: "{{locale()}}"
            });
            form.on('submit(common_form)', function(data){
                let params = {};
                debugger
                $.each(data.field, function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/business/special_goods')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
                return false;
            });
        });

    </script>
@endsection
