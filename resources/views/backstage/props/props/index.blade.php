@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">Name:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="name" id="name" value="@if(!empty($name)){{$name}}@endif" />
                </div>
                <label class="layui-form-label">Category:</label>
                <div class="layui-input-inline">
                    <select  name="category">
                        <option value="">All</option>
                        @foreach($categories as $cate)
                            <option value="{{$cate->name}}" @if(isset($category) && $category==$cate->name) selected @endif>{{$cate->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                <button id="add" type="button" class="layui-btn layui-btn-normal">Add</button>
            </div>
        </form>
        <table class="layui-table"   lay-filter="props_table" id="props_table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:80 , fixed: 'left'}">ID</th>
                <th lay-data="{field:'cover', width:80, fixed: 'left'}">Image</th>
                <th lay-data="{field:'name', width:150,sort:true, fixed: 'left'}">Name</th>
                <th lay-data="{field:'recommendation', width:120}">Recommend</th>
                <th lay-data="{field:'category', width:100}">Category</th>
                <th lay-data="{field:'camera', width:80}">Camera</th>
                <th lay-data="{field:'hot', width:100}">Hot</th>
                <th lay-data="{field:'hash', width:280}">Hash(MD5)</th>
                <th lay-data="{field:'is_delete', width:100}">Status</th>
                <th lay-data="{field:'sort', width:100,edit: 'text',sort:true}">Sort</th>
                <th lay-data="{field:'url', width:360}">URL</th>
                <th lay-data="{field:'created_at', width:160}">Created_at</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td><img style="width: 33px;" src="{{$value->cover}}"></td>
                    <td>{{$value->name}}</td>
{{--                    <td>{{$value->type}}</td>--}}
                    <td><input type="checkbox" @if($value->recommendation==1) checked @endif name="recommendation" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->category}}</td>
                    <td>{{$value->camera}}</td>
                    <td><input type="checkbox" @if($value->hot==1) checked @endif name="hot" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->hash}}</td>
                    <td><input type="checkbox" @if($value->is_delete==0) checked @endif name="is_delete" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td>{{$value->url}}</td>
                    <td>{{$value->sort}}</td>
                    <td>{{$value->created_at}}</td>
{{--                    <td>{{$value->updated_at}}</td>--}}
{{--                    <td>@if($value->deleted_at=='0000-00-00 00:00:00')@else{{$value->deleted_at}}@endif</td>--}}
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
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common;
            table.on('tool(props_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
               if(layEvent === 'edit'){ //编辑
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: 'Props settings',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['60%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/props/props/'+id+'/edit',
                    });
                }
            });

            //监听单元格编辑
            table.on('edit(props_table)', function(obj){
                var that = this;
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field //得到字段
                    ,original = $(this).prev().text(); //得到字段
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::props.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/props/props')}}/"+data.id , params , function(res){
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
                var propsId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('props::props.update'))
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
                    common.ajax("{{url('/backstage/props/props')}}/"+propsId , JSON.parse(params) , function(res){
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
                    title: 'Props settings',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['60%','90%'],
                    offset: 'auto',
                    content: '/backstage/props/props/create',
                });
            });
            table.init('props_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection
