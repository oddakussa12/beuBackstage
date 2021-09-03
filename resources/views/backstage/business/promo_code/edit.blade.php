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
                        <input class="layui-input" type="text" name="description" lay-verify="required" required placeholder="{{trans('business.form.placeholder.promo_code.description')}}" value="{{$result->description}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.promo_code')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="id" name="id" value="{{$result->id}}">
                        <input class="layui-input" type="text" name="promo_code" lay-verify="required" required placeholder="{{trans('business.form.placeholder.promo_code.promo_code')}}" value="{{$result->promo_code}}" disabled>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.free_delivery')}}：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1">YES</option>
                            <option value="0" @if(empty($result->free_delivery)) selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.deadline')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="date" required placeholder="{{trans('common.form.label.date')}}" id="deadline" name="deadline" value="{{$result->deadline}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.discount_type')}}：</label>
                    <div class="layui-input-inline">
                        <select lay-filter="select" id="discount_type" name="discount_type">
                            @foreach(trans('business.form.select.promo_code') as $k=>$v)
                                <option value="{{$k}}" @if($result->discount_type==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.reduction')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" placeholder="Reduction" id="reduction" name="reduction" value="{{$result->reduction}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.percentage')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" max="100" placeholder="Percentage" id="percentage" name="percentage" value="{{$result->percentage}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.limit')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" placeholder="Limit" name="limit" value="{{$result->limit}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.promo_code.goods_id')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" min="0" max="100"  id="goods_id" name="goods_id"  value="{{$result->goods_id}}">
                    </div>
                </div>
                <div class="layui-inline">
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
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common', 'table', 'layer', 'laydate'], function () {
            var form = layui.form,
                layer = layui.layer,
                laydate = layui.laydate,
                common = layui.common,
                $=layui.jquery;
            laydate.render({
                elem: '#deadline'
                ,min : 'today'
                ,type: 'date'
                ,lang: "{{locale()}}"
            });

            form.on('submit(prop_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });

                let id = params.id;
                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/promo_code')}}/"+id, params , function(res){
                    parent.location.reload();
                } , 'PATCH');
                return false;
            });
        });

    </script>
@endsection
