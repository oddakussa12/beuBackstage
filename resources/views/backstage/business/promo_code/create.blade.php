@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-fluid">
        <form class="layui-form">
            {{ csrf_field() }}
            <br>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.description')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="description" lay-verify="required" required placeholder="{{trans('business.form.placeholder.promo_code.description')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.promo_code')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="promo_code" lay-verify="required" required placeholder="{{trans('business.form.placeholder.promo_code.promo_code')}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.free_delivery')}}：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.deadline')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="date" required placeholder="{{trans('common.form.label.date')}}" id="deadline" name="deadline">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.discount_type')}}：</label>
                    <div class="layui-input-inline">
                        <select lay-filter="select" id="discount_type" name="discount_type">
                            @foreach(trans('business.form.select.promo_code') as $k=>$v)
                                <option value="{{$k}}">{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.reduction')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" placeholder="{{trans('business.form.placeholder.promo_code.reduction')}}" id="reduction" name="reduction">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.percentage')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" max="100"  placeholder="{{trans('business.form.placeholder.promo_code.percentage')}}" id="percentage" name="percentage">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.limit')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" placeholder="{{trans('business.form.placeholder.promo_code.limit')}}" name="limit">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" type="submit"  lay-submit lay-filter="prop_form" >{{trans('common.form.button.update')}}</button>
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
                ,type: 'date'
                ,lang: 'en'
            });
            form.on('submit(prop_form)', function(data){
                let params = {};
                debugger
                $.each(data.field, function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                common.ajax("{{url('/backstage/business/promo_code')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
                return false;
            });
        });

    </script>
@endsection
