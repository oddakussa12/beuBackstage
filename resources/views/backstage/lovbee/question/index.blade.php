@extends('layouts.dashboard')
@section('layui-content')
    <style>a{margin-right: 20px;color: #01AAED}</style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-inline">
                <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                <button id="add" type="button" class="layui-btn layui-btn-normal">Add</button>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:100}">ID</th>
                <th lay-data="{field:'title', minWidth:200}">Title</th>
                <th lay-data="{field:'status', minWidth:150,}">Status</th>
                <th lay-data="{field:'sort', width:100,edit: 'text',sort:true}">Sort</th>
                <th lay-data="{field:'url', minWidth:130}">Url</th>
                <th lay-data="{field:'content', width:500,sort:true}">Language</th>
                <th lay-data="{field:'created_at', minWidth:160}">CreatedAt</th>
                <th lay-data="{fixed: 'right', minWidth:160, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>@if(is_array($value->title))@foreach($value->title as $key=>$item)
                            {{$key}}： {{$item}} <br />
                        @endforeach
                        @endif
                    </td>
                    <td><input type="checkbox" @if($value->status==1) checked @endif name="status" lay-skin="switch" lay-filter="switchAll" lay-text="ONLINE|OFFLINE"></td>
                    <td>{{$value->sort}}</td>
                    <td>@if(is_array($value->url))@foreach($value->url as $key=>$item)
                            <a target="_blank" href="{{$item}}">{{$key}}</a>
                            @endforeach
                            @endif
                    </td>
                    <td>@if(is_array($value->content))@foreach($value->content as $key=>$item)
                            {{$key}}{{$item}} <br />
                        @endforeach
                        @endif
                    </td>
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
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let tr = obj.tr; //获得当前行 tr 的DOM对象
                let id = data.id;

                if(layEvent === 'edit'){ //编辑
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%','100%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/lovbee/question/'+id+'/edit',
                    });
                } else {
                    common.confirm("{{trans('common.confirm.update')}}" , function(){
                        common.ajax("{{url('/backstage/lovbee/question/upload')}}/"+data.id , '' , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                            table.render();
                            location.reload();
                        } , 'get' , function (event,xhr,options,exc) {
                            setTimeout(function(){
                                common.init_error(event,xhr,options,exc);
                                table.render();
                                },100);
                        });
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                        table.render();
                    });
               }
            });

            //监听单元格编辑
            table.on('edit(table)', function(obj){
                var that = this;
                let value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field //得到字段
                    ,original = $(this).prev().text(); //得到字段
                let params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('lovbee::question.update'))
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
                let checked = data.elem.checked;
                let id = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('lovbee::question.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                @endif;
                let name = $(data.elem).attr('name');
                let val  = checked ? 1 : 0;
                let params = '{"'+name+'":"'+val+'"}';
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/lovbee/question')}}/"+id , JSON.parse(params) , function(res){
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
        <a class="layui-btn layui-btn-xs" lay-event="upload">{{trans('common.table.button.upload')}}</a>
    </script>
@endsection
