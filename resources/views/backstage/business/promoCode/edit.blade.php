@extends('layouts.app')
@section('title', trans('common.header.title'))
@section('content')
    <div class="layui-fluid">
        <form class="layui-form">
            {{ csrf_field() }}
            <br>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Description：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="description" lay-verify="required" required placeholder="Description" value="{{$result->description}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">PromoCode：</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="id" name="id" value="{{$result->id}}">
                        <input class="layui-input" type="text" name="promo_code" lay-verify="required" required placeholder="PromoCode" value="{{$result->promo_code}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">FreeDelivery：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1">YES</option>
                            <option value="0" @if(empty($result->free_delivery)) selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">DeadLine：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="date" required placeholder="{{trans('common.form.label.date')}}" id="deadline" name="deadline" value="{{$result->deadline}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">DiscountType：</label>
                    <div class="layui-input-inline">
                        <select name="discount_type">
                            <option value="reduction">Reduction</option>
                            <option value="discount" @if($result->discount_type=='discount') selected @endif>Discount</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Reduction：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" lay-verify="number" required  placeholder="Reduction" name="reduction" value="{{$result->reduction}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Percentage：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" lay-verify="number" required  placeholder="Percentage" name="percentage" value="{{$result->percentage}} @endif">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Limit：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" placeholder="Limit" name="limit" value="{{$result->limit}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="prop_form" id="btn">Submit</button>
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
                $.each(data.field , function (k ,v) {
                    if(v===''||v===undefined) {return true;}
                    params[k] = v;
                });
                let id = $('#id').val();
                common.ajax("{{url('/backstage/business/promocode')}}/"+id, params , function(res){
                    parent.location.reload();
                } , 'PATCH');
                return false;
            });
        });

    </script>
@endsection
