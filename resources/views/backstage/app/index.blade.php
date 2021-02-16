@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="app_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:100}">ID</th>
                <th  lay-data="{field:'platform', width:180}">平台</th>
                <th  lay-data="{field:'type', width:150}">类型</th>
                <th  lay-data="{field:'version', width:200 , edit: 'text'}">版本</th>
                <th  lay-data="{field:'upgrade_type', width:200}">更新类型</th>
                <th  lay-data="{field:'upgrade_point', width:300 , event: 'point'}">upgrade_point</th>
            </tr>
            </thead>
            <tbody>
            @foreach($apps as $app)
                <tr>
                    <td>{{ $app->id }}</td>
                    <td>{{ $app->platform }}</td>
                    <td>{{ $app->type }}</td>
                    <td>{{ $app->version }}</td>
                    <td>
                        <input type="checkbox" @if($app->upgrade_type>0) checked @endif name="upgrade_type" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO">
                    </td>
                    <td>{!! $app->upgrade_point !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('footerScripts')
    @parent

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer;




            table.init('app_table', { //转化静态表格
                page:true
            });

            table.on('tool(app_table)', function(obj){
                var data = obj.data;
                var upgrade_point = data.upgrade_point;
                if(obj.event === 'point'){
                    layer.prompt({
                        formType: 2,
                        value: upgrade_point.replace(/\\n/g , "\r\n"),
                        area: ['500px', '300px'] //自定义文本域宽高
                    }, function(value, index, elem){
                        layer.close(index);
                        var v = value.replace(/↵/g , "\\n");
                        var params = {field:"upgrade_point" , value:v};
                        common.ajax("{{url('/backstage/app')}}/"+data.id , params , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        } , 'put');
                        obj.update({
                            upgrade_point: value
                        });
                    });
                }
            });


            table.on('edit(app_table)', function(obj){
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field; //得到字段
                var params = {field:field , value:value};
                common.ajax("{{url('/backstage/app')}}/"+data.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                } , 'put');
                //layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
            });

            form.on('switch(switchAll)', function(data) {
                var checked = data.elem.checked;
                var appId = data.othis.parents('tr').find("td :first").text();
                console.log(appId);
                data.elem.checked = !checked;
                @if(!Auth::user()->can('app.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                        @endif
                var name = $(data.elem).attr('name');
                if (checked) {
                    // var params = '{"' + name + '":"on"}';
                    var params = {"field":name , "value":"on"};
                } else {
                    // var params = '{"' + name + '":"off"}';
                    var params = {"field":name , "value":"off"};
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    layer.closeAll();
                    common.ajax("{{url('/backstage/app')}}/"+appId , params , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});

            });


        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
