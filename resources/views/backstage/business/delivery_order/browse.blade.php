@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-bg-yellow{
            background-color:yellow;
        }
        .layui-bg-pink{
            background-color:pink;
        }
        .layui-bg-green{
            background-color:green;
        }
        .layui-bg-blue{
            background-color:blue;
        }
        .layui-badge-gray{
            background-color:gray;
        }
        textarea.layui-textarea.layui-table-edit {
            min-width: 300px;
            min-height: 200px;
            z-index: 2;
        }
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?status={{$status}}&admin_id=0" class="layui-btn @if(isset($admin_id)&&$admin_id==0) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($admins as $admin)
                            <a href="?status={{$status}}&admin_id={{$admin->admin_id}}" class="layui-btn @if(isset($admin_id)&&$admin_id==$admin->admin_id) layui-btn-disabled @else layui-btn-warm @endif layui-btn-sm" target="_self">{{$admin->admin_username}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">

                    <div class="layui-btn-group">
                        <a href="?status=0&admin_id={{$admin_id}}" class="layui-btn @if(isset($status)&&$status=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.All')}}</a>
                        @foreach($statuses as $key=>$value)
                            @if($key>=5)
                                <a href="?status={{$key}}&admin_id={{$admin_id}}" class="layui-btn @if(isset($status)&&$status==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{trans('business.table.header.shop_order.'.$value)}}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">{{trans('business.table.header.order.order_id')}}</th>
                <th lay-data="{field:'shop_name', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'user_name', minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
                <th lay-data="{field:'order_status', minWidth:180, event:'updateStatus'}">{{trans('business.table.header.order.schedule')}}</th>
                <th lay-data="{field:'order_menu', minWidth:200}">{{trans('business.table.header.delivery_order.menu')}}</th>
                <th lay-data="{field:'order_price', minWidth:120}">{{trans('business.table.header.order.order_price')}}</th>
                <th lay-data="{field:'order_shop_price', minWidth:120}">{{trans('business.table.header.delivery_order.shop_price')}}</th>
                <th lay-data="{field:'order_time', minWidth:180}">{{trans('business.table.header.order.order_time_consuming')}}</th>
                <th lay-data="{field:'comment', minWidth:160, edit: 'textarea'}">{{trans('business.table.header.delivery_order.comment')}}</th>
                <th lay-data="{field:'admin_username', minWidth:100}">Operator</th>
                <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
                <th lay-data="{field:'order_created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:160}">{{trans('common.table.header.updated_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>@if(!empty($order->shop->user_nick_name)){{$order->shop->user_nick_name}}@endif</td>
                    <td>{{$order->user_name}}</td>
                    <td><span class="layui-bg-{{$colorStyles[$order->status]}} layui-btn layui-btn-sm ">{{trans('business.table.header.shop_order.'.$statuses[$order->status])}}</span></td>
                    <td>@if(!empty($order->menu)){{$order->menu}}@endif</td>
                    <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                    <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                    <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                    <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                    <td>@if(!empty($order->operator)){{$order->operator->admin_username}}@endif</td>
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
            base: "{{url('plugin/layui')}}/",
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['table' , 'dropdown' , 'common'], function () {
            const table = layui.table,
                dropdown = layui.dropdown,
                common = layui.common,
            $ = layui.jquery;
            table.init('table', {
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
                    @if(!Auth::user()->can('business::delivery_order.update'))
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
                                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/delivery_order')}}/"+data.id, params, function(res){
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
                @if(!Auth::user()->can('business::delivery_order.update'))
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
                    common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/delivery_order')}}/"+data.id, params , function(res){
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

            setTimeout(function() {
                location.reload();
            }, 300000);
        });
    </script>
@endsection