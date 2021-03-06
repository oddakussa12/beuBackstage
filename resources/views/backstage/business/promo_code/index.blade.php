@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
    </style>
    <div class="layui-fluid">
        <button class="layui-btn" id="add">{{trans('common.form.button.add')}}</button>
        <button class="layui-btn" id="rank">{{trans('business.form.button.promo_code.rank')}}</button>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', minWidth:180, hide:'true'}"></th>
                <th lay-data="{field:'description', minWidth:180}">{{trans('business.table.header.promo_code.description')}}</th>
                <th lay-data="{field:'promo_code', minWidth:130}">{{trans('business.table.header.promo_code.promo_code')}}</th>
                <th lay-data="{field:'free_delivery', minWidth:110}">{{trans('business.table.header.promo_code.free_delivery')}}</th>
                <th lay-data="{field:'deadline', minWidth:110}">{{trans('business.table.header.promo_code.deadline')}}</th>
                <th lay-data="{field:'discount_type', minWidth:130}">{{trans('business.table.header.promo_code.discount_type')}}</th>
                <th lay-data="{field:'reduction', minWidth:110}">{{trans('business.table.header.promo_code.reduction')}}</th>
                <th lay-data="{field:'percentage', minWidth:110}">{{trans('business.table.header.promo_code.percentage')}}</th>
                <th lay-data="{field:'limit', minWidth:100}">{{trans('business.table.header.promo_code.limit')}}</th>
                <th lay-data="{field:'created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{field:'goods_id', minWidth:180, hide:'true'}"></th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promoCodes as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->promo_code}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($value->free_delivery)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($value->free_delivery)) YES @else NO @endif</span></td>
                    <td>{{$value->deadline}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->discount_type=='reduction') layui-btn-normal @else layui-btn-warm @endif">{{$value->discount_type}}</span></td>
                    <td>{{$value->reduction}}</td>
                    <td>{{$value->percentage}}</td>
                    <td>{{$value->limit}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->updated_at}}</td>
                    <td>{{$value->goods_id}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $promoCodes->links('vendor.pagination.default') }}
        @else
            {{ $promoCodes->appends($appends)->links('vendor.pagination.default') }}
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
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}"
                },
            });
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"

                let data = obj.data; //?????????????????????
                let layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                if(layEvent === 'edit'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/promo_code')}}/"+data.id);
                }
                if(layEvent === 'delete'){
                    common.confirm("{{trans('common.confirm.delete')}}" , function(){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/promo_code')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        }, 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }
            });
            $(document).on('click','#add',function(){
                common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/promo_code/create')}}");
            });
            $(document).on('click','#rank',function(){
                common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/promo_code/rank')}}");
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