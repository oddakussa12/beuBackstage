@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'translation_key', width:360}">{{trans('translation.table.header.translation_key')}}</th>
                <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                <th  lay-data="{field:'{{ $locale }}', width:300 , edit: 'text'}">{{ $locale }}</th>
                <?php endforeach; ?>
                <th  lay-data="{field:'translation_op', minWidth:80 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($translations as $key=>$translation)
                <tr>
                    <td>{{ $key }}</td>
                    <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                    <td>{{ is_array(array_get($translation, $locale, null)) ?: array_get($translation, $locale, null) }}</td>
                    <?php endforeach; ?>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="layui-hide" id="confirmTpl">
        <form  class="layui-form" lay-filter="translation_form" >
            <input type="hidden" name="key" />
            <input type="hidden" name="lang" />
            <div class="layui-input-block">
                <label class="layui-form-label">{{trans('permission.form.label.name')}}</label>
                <div class="layui-input-inline">
                    <input type="text" name="translation_value" autocomplete="off" class="layui-input">
                </div>
            </div>
        </form>
        </div>
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
                page:{
                    layout: ['prev', 'page', 'next'],
                    limit:100
                }
            });

            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'del'){
                    common.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/permission')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }else if(layEvent === 'menu_toggle')
                {
                    treetable.toggleRows($(this).find('.treeTable-icon'), false);
                }
            });

            table.on('edit(table)', function(obj){
                var value = obj.value
                    ,data = obj.data
                    ,field = obj.field;
                var params = {translation_value:value , locale:field};
                common.ajax("{{url('/backstage/translation/')}}/"+data.translation_key , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                } , 'put');
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

                common.ajax("{{url('/backstage/permission')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                        location.reload();
                    });
                });
                return false;
            });
            form.on('submit(form_submit_update)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined)
                    {
                        return true;
                    }
                    params[k] = v;
                });

                common.ajax("{{url('/backstage/permission/')}}/"+params.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
                return false;
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
