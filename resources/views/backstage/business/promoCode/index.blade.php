@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
    </style>
    <div class="layui-fluid">
        <button class="layui-btn" id="add">{{trans('common.form.button.add')}}</button>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', minWidth:180}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'description', minWidth:180}">Description</th>
                <th lay-data="{field:'promo_code', minWidth:180}">PromoCode</th>
                <th lay-data="{field:'free_delivery', minWidth:180}">FreeDelivery</th>
                <th lay-data="{field:'deadline', minWidth:180}">DeadLine</th>
                <th lay-data="{field:'discount_type', minWidth:180}">DiscountType</th>
                <th lay-data="{field:'reduction', minWidth:180}">Reduction</th>
                <th lay-data="{field:'limit', minWidth:180}">Limit</th>
                <th lay-data="{field:'created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->promo_code}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($value->free_delivery)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($value->free_delivery)) YES @else NO @endif</span></td>
                    <td>{{$value->deadline}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->discount_type=='reduction') layui-btn-normal @else layui-btn-warm @endif">{{$value->discount_type}}</span></td>
                    <td>@if(!empty($value->reduction)){{$value->reduction}}@else {{$value->percentage}} @endif</td>
                    <td>{{$value->limit}}</td>
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
                    open(['50%', '90%'], '/backstage/business/promocode/'+data.id);
                }
                if(layEvent === 'delete'){
                    common.confirm("{{trans('common.confirm.delete')}}" , function(){
                        common.ajax("{{url('/backstage/business/promocode')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        }, 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });
            $(document).on('click','#add',function(){
                open(['50%', '90%'], '/backstage/business/promocode/create');
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
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection