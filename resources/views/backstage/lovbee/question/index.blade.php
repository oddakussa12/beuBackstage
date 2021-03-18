@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-inline">
{{--                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>--}}
                <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                <button id="add" type="button" class="layui-btn layui-btn-normal">Add</button>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:100}">ID</th>
                <th lay-data="{field:'title', width:150,sort:true}">Title</th>
                <th lay-data="{field:'category', width:200}">Category</th>
                <th lay-data="{field:'status', minWidth:150,}">Status</th>
                <th lay-data="{field:'sort', width:100,edit: 'text',sort:true}">Sort</th>
                <th lay-data="{field:'content', width:500,sort:true}">Language</th>
                <th lay-data="{field:'created_at', width:160}">CreatedAt</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->title}}</td>
                    <td>{{$value->catetory}}</td>
                    <td><input type="checkbox" @if($value->status==1) checked @endif name="status" lay-skin="switch" lay-filter="switchAll" lay-text="ONLINE|OFFLINE"></td>
                    <td>{{$value->sort}}</td>
                    <td>{{$value->content}}</td>
                    <td>{{$value->created_at}}</td>
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
            common: 'lay/modules/admin/common'
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common;
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
               if(layEvent === 'edit'){ //编辑
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: 'Category',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%','100%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/lovbee/question/'+id+'/edit',
                    });
                }
            });

            //监听单元格编辑
            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field //得到字段
                    ,original = $(this).prev().text(); //得到字段
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::category.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/lovbee/question')}}/"+data.id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                    } , 'PATCH' , function (event,xhr,options,exc) {
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

            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var categoryId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('lovbee::question.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                var name = $(data.elem).attr('name');
                if(checked) {
                    var params = '{"'+name+'":"on"}';
                }else {
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/lovbee/question')}}/"+categoryId , JSON.parse(params) , function(res){
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
            $(document).on('click','#add',function(){
                layer.open({
                    type: 2,
                    title: 'Category',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%','100%'],
                    offset: 'auto',
                    content: '/backstage/lovbee/question/create',
                });
            });
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection
