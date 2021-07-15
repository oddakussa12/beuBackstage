@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table-select-dl { color: black}
        textarea.layui-textarea.layui-table-edit {min-width: 300px; min-height: 200px; z-index: 2;}
        table tr td select {border: none; height: 29px;}
        table tr td select option{ color: #000000; background: #FFFFFF;}
        .layui-menu{margin-top:-5px;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?status={{$status}}&user_id=0" class="layui-btn @if(isset($userId)&&$userId==0) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($shops as $shop)
                            <a href="?status={{$status}}&user_id={{$shop->user_id}}" class="layui-btn @if(isset($userId)&&$userId==$shop->user_id) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">{{$shop->user_nick_name}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?status=0&user_id=@if(isset($user_id)){{$user_id}}@endif" class="layui-btn @if(isset($status)&&$status=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($statuses as $key=>$value)
                            <a href="?status={{$key}}&user_id=@if(isset($user_id)){{$user_id}}@endif" class="layui-btn @if(isset($status)&&$status==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.'.$value)}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">{{trans('business.table.header.order.order_id')}}</th>
                <th lay-data="{field:'shop', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'shop_contact', minWidth:180}">{{trans('business.table.header.shop.user_contact')}}</th>
                <th lay-data="{field:'shop_address', minWidth:180}">{{trans('business.table.header.shop.user_address')}}</th>
                <th lay-data="{field:'goods_name', minWidth:180}">{{trans('business.table.header.discovery_order.goods_name')}}</th>
                <th lay-data="{field:'order_user_name', maxWidth:180, minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
                <th lay-data="{field:'order_user_contact', minWidth:180}">{{trans('business.table.header.order.user_contact')}}</th>
                <th lay-data="{field:'order_user_address', minWidth:180}">{{trans('business.table.header.order.user_address')}}</th>
                <th lay-data="{field:'order_status', minWidth:170, event:'updateStatus'}">{{trans('business.table.header.order.schedule')}}</th>
                <th lay-data="{field:'order_menu', minWidth:200, edit: 'textarea'}">{{trans('business.table.header.discovery_order.menu')}}</th>
                <th lay-data="{field:'order_price', minWidth:120, edit:'text'}">{{trans('business.table.header.order.order_price')}}</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">{{trans('business.table.header.discovery_order.shop_price')}}</th>
                <th lay-data="{field:'comment', minWidth:160, edit:'textarea'}">{{trans('business.table.header.discovery_order.comment')}}</th>
                <th lay-data="{field:'operator', minWidth:100}">Operator</th>
                <th lay-data="{field:'order_time', minWidth:180}">{{trans('business.table.header.order.order_time_consuming')}}</th>
                <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
                <th lay-data="{field:'order_created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'order_updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>{{$order->shop->user_nick_name}}</td>
                    <td>{{$order->shop->user_contact}}</td>
                    <td>{{$order->shop->user_address}}</td>
                    <td>{{empty($order->g)?'':$order->g->name}}</td>
                    <td>{{$order->user_name}}</td>
                    <td>{{$order->user_contact}}</td>
                    <td>{{$order->user_address}}</td>
                    <td><span class="layui-bg-{{$colorStyles[$order->status]}} layui-btn layui-btn-sm ">{{trans('business.table.header.shop_order.'.$statuses[$order->status])}}</span></td>
                    <td>@if(!empty($order->menu)){{$order->menu}}@endif</td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->operator)){{$order->operator->admin_username}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}} @endif</td>
                    <td>@if(!empty($order->color)){{$order->color}}@endif</td>
                    <td>{{$order->created_at}}</td>
                    <td>{{$order->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $orders->links('vendor.pagination.default') }}
        @else
            {{ $orders->appends($appends)->links('vendor.pagination.default') }}
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
        }).use(['common', 'table', 'dropdown', 'layer'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                dropdown = layui.dropdown,
                $ = layui.jquery;
            var order = table.init('table', { //转化静态表格
                page:false,
                done: function(res, curr, count){
                    console.log(res.data);
                    $('th').css({'font-size': '15'});	//进行表头样式设置
                    for(var i in res.data){		//遍历整个表格数据
                        var item = res.data[i];		//获取当前行数据
                        if(item.color==1){
                            $("tr[data-index='" + i + "']").attr({"style":"background:#ff3333; color:#fff"});  //将当前行变成绿色
                        }
                    }
                }
            });
            table.on('tool(table)', function (obj) {
                let  selector = obj.tr.selector,data = obj.data;
                if (obj.event ==='updateStatus') {
                    @if(!Auth::user()->can('business::discovery_order.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(".layui-table-box "+selector+" td[data-field=order_status]"));
                    table.render();
                    return true;
                    @endif
                    dropdown.render({
                        elem: this
                        ,show: true
                        ,data: @json($statusKv)
                        ,click: function(obj){
                            var params = {'status':obj.id};
                            common.confirm("{{trans('common.confirm.update')}}" , function(){
                                common.ajax("{{url('/backstage/business/discovery_order')}}/"+data.id, params, function(res){
                                    location.reload();
                                }, 'patch');
                            } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                                table.render();
                            });
                        }
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
                @if(!Auth::user()->can('business::discovery_order.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                    obj.update(d);
                    $(this).val(original);
                    table.render();
                    return true;
                @endif
                    params[field] = value;
                    params['id'] = data.id;
                if (field=='order_price') {
                    let arg = /^\d+(\.\d+)?$/;
                    if (!arg.exec(value)) {
                        layer.alert('You can only enter numbers and decimals', {icon: 5});
                        return false;
                    }
                    if (value <= 30) {
                        layer.alert('The price cannot be lower than 30', {icon: 5});
                        return false;
                    }
                }
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/discovery_order')}}/"+data.id, params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                        parent.location.reload();
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
            form.render();
            setTimeout(function() {
                location.reload();
            }, 300000);
        });
    </script>
@endsection