@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
    </style>
    <div class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="created_at">CreatedAt</option>
                            <option value="sort"       @if(isset($sort) && $sort=='sort') selected @endif>Sort</option>
                            <option value="goods_num"  @if(isset($sort) && $sort=='goods_num') selected @endif>GoodsNum</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">IsDefault:</label>
                    <div class="layui-input-inline">
                        <select name="default">
                            <option value="">All</option>
                            <option value="1" @if(isset($default) && $default=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($default) && $default=='0') selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <button class="layui-btn layui-btn-normal" id="add">{{trans('common.form.button.add')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'category_id', minWidth:180, hide:'true'}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'name', minWidth:180}">{{trans('common.form.label.name')}}</th>
                <th lay-data="{field:'goods_num', minWidth:130}">{{trans('business.table.header.goods_num')}}</th>
                <th lay-data="{field:'default', minWidth:110}">IsDefualt</th>
                <th lay-data="{field:'sort', minWidth:110, edit:'text'}">{{trans('common.form.label.sort')}}</th>
                <th lay-data="{field:'created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->category_id}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->goods_num}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($value->default)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($value->default)) YES @else NO @endif</span></td>
                    <td>{{$value->sort}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $result->links('vendor.pagination.default') }}
        @else
            {{ $result->appends($appends)->links('vendor.pagination.default') }}
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
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                },
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"

                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'edit'){
                    open(['50%', '90%'], '/backstage/business/category/goods/'+data.id);
                }
                if(layEvent === 'delete'){
                    common.confirm("{{trans('common.confirm.delete')}}" , function(){
                        common.ajax("{{url('/backstage/business/category/goods')}}/"+data.category_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        }, 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });
            $(document).on('click','#add',function(){
                open(['50%', '90%'], '/backstage/business/category/goods/create');
            });
            function open(area, content, types=2) {
                layer.open({
                    type: types,
                    shadeClose: true,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    content: content,
                });
            }
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection