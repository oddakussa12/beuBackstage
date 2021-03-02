@extends('layouts.dashboard')
@section('layui-content')
    <style>
       /* .layui-table-cell {
            font-size:14px;
            padding:0 5px;
            height:auto;
            overflow:visible;
            text-overflow:inherit;
            white-space:normal;
            word-break: break-all;
        }*/
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-inline">
{{--                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>--}}
                <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                <button id="add" type="button" class="layui-btn layui-btn-normal">添加</button>
            </div>
        </form>
        <table class="layui-table"   lay-filter="post_table" id="post_table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:80}">ID</th>
                <th lay-data="{field:'cover', width:80}">图片</th>
                <th lay-data="{field:'name', width:150,sort:true}">名称</th>
{{--                <th lay-data="{field:'type', width:100}">类型</th>--}}
                <th lay-data="{field:'recommendation', width:100}">是否推荐</th>
                <th lay-data="{field:'category', width:80}">类别</th>
                <th lay-data="{field:'camera', width:80}">摄像头</th>
                <th lay-data="{field:'hash', width:280}">Hash(MD5)</th>
                <th lay-data="{field:'is_delete', width:100}">状态</th>
                <th lay-data="{field:'url', width:360}">URL</th>
                <th lay-data="{field:'created_at', width:160}">创建时间</th>
                <th lay-data="{field:'updated_at', width:160}">修改时间</th>
{{--                <th lay-data="{field:'deleted_at', width:160}">删除时间</th>--}}
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#postop'}">{{trans('common.table.header.op')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td><img style="width: 33px;" src="{{$value->cover}}"></td>
                    <td>{{$value->name}}</td>
{{--                    <td>{{$value->type}}</td>--}}
                    <td><input type="checkbox" @if($value->recommendation==1) checked @endif name="recommendation" lay-skin="switch" lay-filter="switchAll" lay-text="是|否"></td>
                    <td>{{$value->category}}</td>
                    <td>{{$value->camera}}</td>
                    <td>{{$value->hash}}</td>
                    <td><input type="checkbox" @if($value->is_delete==0) checked @endif name="is_delete" lay-skin="switch" lay-filter="switchAll" lay-text="上架|下架"></td>
                    <td>{{$value->url}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->updated_at}}</td>
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
               if(layEvent === 'edit'){ //编辑
                    console.log(data);
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: '道具设置',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['60%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/props/props/'+id+'/edit',
                    });
                }
            });
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var postId = data.othis.parents('tr').find("td :first").text();
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
                    common.ajax("{{url('/backstage/props/props')}}/"+postId , JSON.parse(params) , function(res){
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
                    title: '道具设置',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['60%','90%'],
                    offset: 'auto',
                    content: '/backstage/props/props/create',
                });
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
        });
    </script>
    <script type="text/html" id="postop">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection
