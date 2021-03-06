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
                <th lay-data="{field:'id', minWidth:180, hide:'true'}">ID</th>
                <th lay-data="{field:'goods_id', minWidth:180, hide:'true'}">{{trans('business.table.header.goods.id')}}</th>
                <th lay-data="{field:'user_name', minWidth:180}">{{trans('business.table.header.special_goods.user_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:180}">{{trans('business.table.header.special_goods.user_nick_name')}}</th>
                <th lay-data="{field:'goods_name', minWidth:180}">{{trans('business.table.header.goods.name')}}</th>
                <th lay-data="{field:'special_price', minWidth:130}">{{trans('business.table.header.special_goods.special_price')}}</th>
                <th lay-data="{field:'free_delivery', minWidth:110}">{{trans('business.table.header.special_goods.free_delivery')}}</th>
                <th lay-data="{field:'packaging_cost', minWidth:130}">{{trans('business.table.header.special_goods.packaging_cost')}}</th>
                <th lay-data="{field:'status', minWidth:110}">{{trans('business.table.header.special_goods.status')}}</th>
                <th lay-data="{field:'start_time', minWidth:170}">{{trans('business.table.header.special_goods.start_time')}}</th>
                <th lay-data="{field:'deadline', minWidth:170}">{{trans('business.table.header.special_goods.deadline')}}</th>
                <th lay-data="{field:'created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($delaySpecialGoods as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->g->id}}</td>
                    <td>{{$value->shop->user_name}}</td>
                    <td>{{$value->shop->user_nick_name}}</td>
                    <td>{{$value->g->name}}</td>
                    <td>{{$value->special_price}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($value->free_delivery)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($value->free_delivery)) YES @else NO @endif</span></td>
                    <td>{{$value->packaging_cost}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($value->status)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($value->status)) YES @else NO @endif</span></td>
                    <td>{{$value->start_time}}</td>
                    <td>{{$value->deadline}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $delaySpecialGoods->links('vendor.pagination.default') }}
        @else
            {{ $delaySpecialGoods->appends($appends)->links('vendor.pagination.default') }}
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
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table'], function () {
            const table = layui.table,
                common = layui.common,
                $ = layui.jquery;
            table.init('table', {
                page:false,
                toolbar: '#toolbar'
            });
            table.on('tool(table)', function(obj){
                let data = obj.data;
                let layEvent = obj.event;
                if(layEvent === 'edit'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/delay_special_goods/')}}/"+data.id+'/'+layEvent+"?timezone="+Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if(layEvent === 'delete'){
                    common.confirm("{{trans('common.confirm.delete')}}" , function(){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/delay_special_goods')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        }, 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });
            $(document).on('click','#add',function(){
                common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/delay_special_goods/create')}}"+"?timezone="+Intl.DateTimeFormat().resolvedOptions().timeZone);
            });
        });
    </script>
    <script type="text/html" id="op">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>
@endsection