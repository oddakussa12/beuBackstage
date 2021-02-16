@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>热门话题</legend>
        </fieldset>
        <table class="layui-table"   lay-filter="post_table" id="post_table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:100, sort:true}">编号</th>
                <th lay-data="{field:'topic_content', width:250, sort:true}">名称</th>
                <th lay-data="{field:'flag', width:120, sort:true}" >类型</th>
                <th lay-data="{field:'sort', width:100, sort:true}">排序</th>
                <th lay-data="{field:'start_time', width:200, sort:true}">开始时间</th>
                <th lay-data="{field:'end_time', width:200, sort:true}">结束时间</th>
{{--                <th lay-data="{field:'is_delete', width:120, sort:true}">{{trans('post.table.header.post_delete')}}</th>--}}
                <th lay-data="{fixed: 'right', minWidth:250, align:'center', toolbar: '#postop'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->topic_content}}</td>
                    <td>
                        @if($value->flag==1)
                            <div class="layui-btn layui-btn-sm layui-btn-danger">官方话题</div>
                        @elseif ($value->flag==2)
                            <div class="layui-btn layui-btn-sm layui-btn-normal">热门话题</div>
                        @endif
                    </td>
                    <td>{{$value->sort}}</td>
                    <td>@if ($value->start_time){{date('Y-m-d H:i:s', $value->start_time)}}@endif</td>
                    <td>@if ($value->end_time){{date('Y-m-d H:i:s', $value->end_time)}}@endif</td>
{{--                    <td>--}}
{{--                        <input type="checkbox" name="delete" title="删除" @if(!empty($value->is_delete)) checked="" @endif lay-filter="delete">--}}
{{--                    </td>--}}
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $data->links('vendor.pagination.default') }}
        @else
            {{ $data->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker;
            table.on('tool(post_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'delete'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/content/topic')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');
                    });
                } else if(layEvent === 'edit'){ //编辑
                    console.log(data);
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: '修改',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['50%','90%'],
                        offset: 'auto',
                        content: '/backstage/content/topic/'+id+'/edit',
                    });
                    //common.open("{{url(locale().'/backstage/content/topic')}}/"+id+'/edit');
                    //do something
                    //同步更新缓存对应的值
                }
            });
           /* table.on('edit(event_table)', function(obj){
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field; //得到字段
                var params = {value:value , field:field};
                common.ajax("{{url('/backstage/content/topic/')}}/"+data.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                } , 'patch');
                //layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
            });*/
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var postId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('content::topic.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                var name = $(data.elem).attr('name');
                alert(name);
                if(checked) {
                    var params = '{"'+name+'":"on"}';
                }else {
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/content/topic')}}/"+postId , JSON.parse(params) , function(res){
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

            form.on('checkbox(delete)', function(obj){
                var checked = obj.elem.checked;
                console.log(obj.elem.checked);
                var postId = obj.othis.parents('tr').find("td :first").text();
                obj.elem.checked = !checked;
                @if(!Auth::user()->can('content::topic.destroy'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", obj.othis);
                form.render();
                return false;
                @endif form.render();
                var name = $(obj.elem).attr('name');

                layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                    common.ajax("{{url('/backstage/content/topic')}}/"+postId , {} , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                            // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            // layer.close(index);
                            location.reload();
                        });
                    } , 'delete');
                });
                // layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
            });
            table.init('post_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });
            $(document).on('click','#add',function(){
                layer.open({
                    type: 2,
                    title: '热门话题',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['50%','90%'],
                    offset: 'auto',
                    content: '/backstage/content/topic/create',
                });
                //common.open("{{url(locale().'/backstage/content/topic/create')}}");
            });
        });
    </script>
    <script type="text/html" id="toolbar">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px">话题名称:</label>
                    <div class="layui-input-inline" style="width: 200px;">
                        <input class="layui-input" name="v" id="value"  @if(!empty($v)) value="{{$v}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 120px">{{trans('user.form.label.user_created_at')}}:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
{{--                                        <button class="layui-btn" type="button" id="clearCache" >清空缓存</button>--}}
                    <div class="layui-btn layui-btn-primary">共{{$data->total()}}条记录</div>
                    <button id="add" type="button" class="layui-btn layui-btn-normal">添加</button>

                </div>

            </div>
        </form>
    </script>
    <script type="text/html" id="postop">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection
<script>

</script>