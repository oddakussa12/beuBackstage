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
                common = layui.common,
                layer = layui.layer;




            table.init('table', { //转化静态表格
                page:{
                    layout: ['prev', 'page', 'next'],
                    limit:100
                }
            });

            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/permission')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');


                        //向服务端发送删除指令
                    });
                } else if(layEvent === 'edit'){ //编辑

                    console.log(1);

                }else if(layEvent === 'menu_toggle')
                {
                    treetable.toggleRows($(this).find('.treeTable-icon'), false);
                }
                else if(layEvent === 'setValue') {
                    // layer.prompt({
                    //     formType: 2
                    //     ,title: data.translation_key
                    //     ,value: $(this).text()
                    // }, function(value, index , elem){
                    //     layer.close(index);
                    //     console.log(elem);
                    //     //这里一般是发送修改的Ajax请求
                    //     console.log($(this));
                    //     //同步更新表格和缓存对应的值

                    // });
                    var translation_field = $(this).text();
                    var lang = $(this).attr('data-field');
                    layer.open({
                        id:'translation'
                        ,type:1,
                        content:$('#confirmTpl').html()
                        ,btn: ['确定', '取消']
                        ,btnAlign: 'c'
                        ,closeBtn:0
                        ,zIndex:1000000
                        ,yes: function(index, layero){

                            //按钮【按钮一】的回调
                            var translation_value = $('#translation input[name=translation_value]').val();
                            var key = $('#translation input[name=key]').val();
                            var str = '{"'+lang+'":"'+translation_value+'"}';
                            var params = {value:translation_value , locale:lang};
                            common.ajax("{{url('/backstage/translation/')}}/"+key, params , function(res){
                                common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                                    location.reload();
                                });
                            } , 'put');
                            obj.update(JSON.parse(str));
                            layer.close(index);

                        }
                        ,btn2: function(index, layero){
                            //按钮【按钮二】的回调

                            //return false 开启该代码可禁止点击该按钮关闭
                        }
                        ,cancel: function(){
                            //右上角关闭回调

                            //return false 开启该代码可禁止点击该按钮关闭
                        }
                        ,success: function(layero, index){
                            form.val("translation_form", {
                                "key": data.translation_key
                                ,'lang':lang
                                ,"translation_value": translation_field
                            });
                            form.render();
                        }
                    });
                }
            });

            table.on('edit(table)', function(obj){
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field; //得到字段
                // var str = '{"'+field+'":"'+translation_value+'"}';
                var params = {translation_value:value , locale:field};
                common.ajax("{{url('/backstage/translation/')}}/"+data.translation_key , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                } , 'put');
                //layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
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
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
