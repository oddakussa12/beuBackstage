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
                        <input class="layui-input" type="text" name="description" lay-verify="required" required placeholder="Description">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">PromoCode：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="promo_code" lay-verify="required" required placeholder="PromoCode">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">FreeDelivery：</label>
                    <div class="layui-input-inline">
                        <select name="free_delivery" >
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">DeadLine：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" readonly lay-verify="date" required placeholder="{{trans('common.form.label.date')}}" id="deadline" name="deadline">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">DiscountType：</label>
                    <div class="layui-input-inline">
                        <select lay-filter="select" id="discount_type" name="discount_type">
                            <option value="reduction">Reduction</option>
                            <option value="discount">Discount</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Reduction：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" placeholder="Reduction" id="reduction" name="reduction">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Percentage：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" min="0" max="100"  placeholder="Percentage" id="percentage" name="percentage">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Limit：</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="number" placeholder="Limit" name="limit">
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
            form.on('select', function(data){
                let field = data.value==='discount' ? 'reduction' : 'percentage';
                $("#"+field).val('');
            });
            form.on('submit(prop_form)', function(data){
                let params = {};
                $.each(data.field, function (k ,v) {
                    if(v===''||v===undefined) {return false;}
                    params[k] = v;
                });
                let select = $("#discount_type").val();
                if (select==='discount') {
                    let val = $("#percentage").val();
                    if (val<0 || val>100) {
                        $("#percentage").focus();
                        layer.msg('percentage between 0 and 100');
                        return false;
                    }
                }else {
                    let val = $("#reduction").val();
                    if (!$.isNumeric(val)) {
                        $("#reduction").focus();
                        layer.msg('reduction has to be a number ');
                        return false;
                    }
                }
                common.ajax("{{url('/backstage/business/promocode')}}/", params , function(res){
                    parent.location.reload();
                } , 'post');
                return false;
            });
        });

    </script>
@endsection
