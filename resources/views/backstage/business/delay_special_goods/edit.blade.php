@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-fluid">
        <form class="layui-form">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods.name')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="id" name="id" value="{{$delaySpecialGoods->id}}">
                        <input class="layui-input" type="text" disabled placeholder="{{trans('business.form.placeholder.goods.name')}}" value="{{$delaySpecialGoods->g->name}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.special_price')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="special_price" lay-verify="number" required placeholder="{{trans('business.form.placeholder.special_goods.special_price')}}" value="{{$delaySpecialGoods->special_price}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.packaging_cost')}}：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="id" name="id" value="{{$delaySpecialGoods->id}}">
                        <input class="layui-input" type="text" name="packaging_cost" lay-verify="number" required placeholder="{{trans('business.form.placeholder.special_goods.packaging_cost')}}" value="{{$delaySpecialGoods->packaging_cost}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.free_delivery')}}：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1"  @if(!empty($delaySpecialGoods->free_delivery)) selected @endif>YES</option>
                            <option value="0"  @if(empty($delaySpecialGoods->free_delivery)) selected @endif>NO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.start_time')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="datetime" required id="start_time" name="start_time" value="{{$delaySpecialGoods->start_time}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.special_goods.deadline')}}：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="datetime" required id="deadline" name="deadline" value="{{$delaySpecialGoods->deadline}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="submit"  lay-submit lay-filter="common_form" >{{trans('common.form.button.update')}}</button>
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
                laydate = layui.laydate,
                common = layui.common,
                $=layui.jquery;
            laydate.render({
                elem: '#start_time'
                ,min : 'today'
                ,type: 'datetime'
                ,lang: "{{locale()}}"
                ,value: "{{$delaySpecialGoods->start_time}}"
            });
            laydate.render({
                elem: '#deadline'
                ,min : 'today'
                ,type: 'datetime'
                ,lang: "{{locale()}}"
                ,value: "{{$delaySpecialGoods->deadline}}"
            });
            form.on('submit(common_form)', function(data){
                let params = {};
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                let id = params.id;
                delete params.id;
                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/delay_special_goods')}}/"+id, params , function(res){
                    parent.location.reload();
                } , 'PATCH');
                return false;
            });
        });

    </script>
@endsection
