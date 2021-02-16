@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <table class="layui-table"  lay-filter="message_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:80}">ID</th>
                <th  lay-data="{field:'type', width:120}">类型</th>
                <th  lay-data="{field:'value', width:120}">值</th>
                <th  lay-data="{field:'title', width:120}">标题</th>
                <th  lay-data="{field:'content', width:120}">内容</th>
                <th  lay-data="{field:'image', width:300}">图片</th>
                <th  lay-data="{field:'status', width:120}">状态</th>
                <th  lay-data="{field:'created_at', width:200}">创建时间</th>
                <th  lay-data="{field:'updated_at', width:200}">执行时间</th>
                <th  lay-data="{field:'message_op', minWidth:80 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($messages as $key=>$message)
                <tr>
                    <td>{{ $message->id }}</td>
                    <td>{{ $message->type }}</td>
                    <td>{{ $message->value }}</td>
                    <td>{{ $message->title }}</td>
                    <td>{{ $message->content }}</td>
                    <td>{{ $message->image }}</td>
                    <td>
                        @if($message->status==0)

                            <input type="checkbox"   name="status" lay-skin="switch" lay-filter="switchAll" lay-text="init|ready">

{{--                            <span class="layui-badge layui-bg-gray">init</span>--}}
                        @elseif ($message->status==1)
                            <span class="layui-badge layui-bg-blue">ready</span>
                        @elseif ($message->status==2)
                            <span class="layui-badge layui-bg-orange">running</span>
                        @else
                            <span class="layui-badge layui-bg-green">over</span>
                        @endif
                    </td>
                    <td>{{ $message->created_at }}</td>
                    <td>{{ $message->updated_at }}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $messages->links('vendor.pagination.default') }}
        @else
            {{ $messages->appends($appends)->links('vendor.pagination.default') }}
        @endif

        <form class="layui-form layui-tab-content"  lay-filter="event_form">


            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-block">
                        <select name="type">
                            <option value="text" selected>文本</option>
                            <option value="postDetail" >贴子详情页</option>
                            <option value="h5" >H5</option>
                            <option value="createPost" >发帖页面</option>
                            <option value="topicDetail" >话题详情页</option>
                            <option value="chatDetail" >私信详情页</option>
                            <option value="userDetail" >个人中心页面</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">值</label>
                    <div class="layui-input-block">
                        <input type="text" name="value"  placeholder="" lay-verify="value" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title"  placeholder="" lay-verify="title" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">内容</label>
                    <div class="layui-input-block">
                        <input type="text" name="content"  placeholder="" lay-verify="content" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>




            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="form_submit_add">{{trans('common.form.button.add')}}</button>
                </div>
            </div>
        </form>

@endsection

@section('footerScripts')
    @parent
            <script type="text/html" id="operateTpl">
                <div class="layui-table-cell laytable-cell-1-6">
                    <a class="layui-btn layui-btn-xs" lay-event="image">图片</a>
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
                    table.init('message_table', { //转化静态表格
                        page:false,
                        toolbar: '#toolbar'
                    });
                    table.on('tool(message_table)', function(obj){
                        var data = obj.data; //获得当前行数据
                        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                        var tr = obj.tr; //获得当前行 tr 的DOM对象
                        if(layEvent === 'image'){ //查看
                            var id = data.id;
                            layer.open({
                                type: 2,
                                shadeClose: true,
                                shade: 0.8,
                                area: ['80%','80%'],
                                offset: 'auto',
                                content: "{{url(locale().'/backstage/service/message')}}/"+id+'/image',
                            });
                        }
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

                        common.ajax("{{url('/backstage/service/message')}}" , params , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        });
                        return false;
                    });

                    table.on('edit(message_table)', function(obj){
                        var value = obj.value //得到修改后的值
                            ,data = obj.data //得到所在行所有键值
                            ,field = obj.field; //得到字段
                        var params = {value:value , field:field};
                        common.ajax("{{url('/backstage/service/message')}}/"+data.id , params , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        } , 'put');
                        //layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
                    });

                    form.on('switch(switchAll)', function(data){
                        var checked = data.elem.checked;
                        var messageId = data.othis.parents('tr').find("td :first").text();
                        data.elem.checked = !checked;
                        @if(!Auth::user()->can('service::message.update'))
                        common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                        form.render();
                        return false;
                        @endif
                        var name = $(data.elem).attr('name');
                        if(checked)
                        {
                            var params = '{"'+name+'":"on"}';
                        }else{
                            var params = '{"'+name+'":"off"}';
                        }
                        form.render();
                        common.confirm("{{trans('common.confirm.update')}}" , function(){
                            common.ajax("{{url('/backstage/service/message')}}/"+messageId , JSON.parse(params) , function(res){
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

                });
            </script>
            <script type="text/html" id="toolbar">
                <form class="layui-form">
                </form>
            </script>
@endsection


