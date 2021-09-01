@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:100}">{{trans('business.table.header.delivery_cost.id')}}</th>
                <th  lay-data="{field:'range', minWidth:150}">{{trans('business.table.header.delivery_cost.range')}}</th>
                <th  lay-data="{field:'distance', minWidth:150, edit: 'text'}">{{trans('business.table.header.delivery_cost.distance')}}</th>
                <th  lay-data="{field:'cost', minWidth:150, edit: 'text'}">{{trans('business.table.header.delivery_cost.cost')}}</th>
                <th  lay-data="{field:'op', maxWidth:80 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($costs as $k=> $cost)
                <tr>
                    <td>{{$cost->id}}</td>
                    <td>@if(isset($costs[$k-1]))
                            {{$costs[$k-1]->distance}}      ⟶       {{$cost->distance}}
                        @else
                            0       ⟶       {{$cost->distance}}
                        @endif
                    </td>
                    <td>{{$cost->distance}}</td>
                    <td>{{$cost->cost}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <form class="layui-form layui-tab-content"  lay-filter="form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-block">
                        <input type="hidden" name="id" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.delivery_cost.distance')}}:</label>
                    <div class="layui-input-block">
                        <input type="text" name="distance"  lay-verify="distance" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.delivery_cost.cost')}}:</label>
                    <div class="layui-input-block">
                        <input type="text" name="cost"  lay-verify="cost" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" lay-submit lay-filter="form_submit_add">{{trans('common.form.button.add')}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common;
            table.init('table', {
                page:false
            });

            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'del'){
                    common.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/business/delivery_cost')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });

            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value
                    ,data = obj.data
                    ,field = obj.field
                    ,original = $(this).prev().text(); //得到字段
                var params = {},d={};
                params[field] = value;
                d[field] = original;
                @if(!Auth::user()->can('business::delivery_cost.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/delivery_cost/')}}/"+data.id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        location.reload();
                    } , 'patch' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            obj.update(d);
                            $(that).val(original);
                            table.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                    d[field] = original;
                    obj.update(d);
                    $(that).val(original);
                    table.render();
                });

            });

            form.on('submit(form_submit_add)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined)
                    {
                        return true;
                    }
                    params[k] = v;
                });

                common.ajax("{{url('/backstage/business/delivery_cost')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                        location.reload();
                    });
                });
                return false;
            });
            form.on('submit(role_form)', function(){
                return false;
            });
        })
    </script>
@endsection
