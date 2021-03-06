@extends('layouts.dashboard')
@section('layui-content')
    <style>
        td img {width:35px; height: 30px;}
    </style>
    <div  class="layui-fluid">
        <table class="layui-table"   lay-filter="table" id="table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:100 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'image', width:120}">{{trans('common.table.header.image')}}</th>
                <th lay-data="{field:'name', minWidth:150,sort:true}">{{trans('common.table.header.name')}}</th>
                <th lay-data="{field:'category', width:100}">{{trans('common.table.header.category')}}</th>
                <th lay-data="{field:'score', width:100}">Score</th>
                <th lay-data="{field:'desc', minWidth:250}">{{trans('common.table.header.description')}}</th>
                <th lay-data="{field:'sort', width:100,edit:'text', sort:true}">{{trans('common.form.label.sort')}}</th>
                <th lay-data="{field:'created_at', width:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', width:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>
                        @if(!empty($value->image))<img src="{{$value->image}}" />@endif
                        @if(!empty($value->image_light))<img src="{{$value->image_light}}" />@endif
                    </td>
                    <td>@if(is_array($value->name))@foreach($value->name as $key=>$item)
                            {{$key}}: {{$item}} <br />
                        @endforeach
                        @endif
                    </td>
                    <td>{{$value->category}}</td>
                    <td>{{$value->score}}</td>
                    <td>@if(is_array($value->desc))@foreach($value->desc as $key=>$item)
                            {{$key}}: {{$item}} <br />
                        @endforeach
                        @endif
                    </td>
                    <td>{{$value->sort}}</td>
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
        //??????????????????
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
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                var data = obj.data; //?????????????????????
                var layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                var tr = obj.tr; //??????????????? tr ???DOM??????
                if(layEvent === 'edit'){ //??????
                    var id = data.id;
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%','90%'],
                        offset: 'auto',
                        scrollbar:true,
                        content: '/backstage/props/medal/'+id+'/edit',
                    });
                }
            });

            //?????????????????????
            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value //?????????????????????
                    ,data = obj.data //???????????????????????????
                    ,field = obj.field //????????????
                    ,original = $(this).prev().text(); //????????????
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::medal.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{LaravelLocalization::localizeUrl('/backstage/props/medal')}}/"+data.id , params , function(res){
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
            table.on('edit(props_table)', function(obj){
                let that = this;
                let value = obj.value //?????????????????????
                    ,data = obj.data //???????????????????????????
                    ,field = obj.field //????????????
                    ,original = $(this).prev().text(); //????????????

                let params, d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::medal.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{LaravelLocalization::localizeUrl('/backstage/props/medal')}}/"+data.id , params , function(res){
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

            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection
