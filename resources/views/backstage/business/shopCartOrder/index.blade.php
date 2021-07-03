@extends('layouts.dashboard')
@section('layui-content')
    <style>
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
                        <a href="?type={{$type}}&user_id=0&status=@if(isset($status)){{$status}}@endif" class="layui-btn @if(isset($user_id)&&$user_id==0) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">All</a>
                        @foreach($shops as $shop)
                            <a href="?type={{$type}}&user_id={{$shop->user_id}}&status=@if(isset($status)){{$status}}@endif" class="layui-btn @if(isset($user_id)&&$user_id==$shop->user_id) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">{{$shop->user_nick_name}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type={{$type}}&user_id=@if(isset($user_id)){{$user_id}}@endif&status=" class="layui-btn @if(!isset($status)) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">All</a>
                    @foreach($orderStatus as $key=>$value)
                            <a href="?type={{$type}}&user_id=@if(isset($user_id)){{$user_id}}@endif&status={{$key}}" class="layui-btn @if(isset($status)&&$status==$key) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">{{$value}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type=0&user_id=@if(isset($user_id)){{$user_id}}@endif" class="layui-btn @if(isset($type)&&$type=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">All</a>
                        @foreach($schedule as $key=>$value)
                            <a href="?type={{$key}}&user_id=@if(isset($user_id)){{$user_id}}@endif&status=@if(isset($status)){{$status}}@endif" class="layui-btn @if(isset($type)&&$type==$key) layui-btn-disabled @else layui-btn-normal @endif" target="_self">{{$value}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
        <thead>
        <tr>
            <th lay-data="{field:'id', width:180}">OrderId</th>
            <th lay-data="{field:'status', minWidth:120}">OrderProcess</th>
            <th lay-data="{field:'shop', minWidth:180}">ShopName</th>
            <th lay-data="{field:'shop_contact', minWidth:180}">ShopPhone</th>
            <th lay-data="{field:'shop_address', minWidth:180}">ShopAddress</th>
            <th lay-data="{field:'order_user_name', maxWidth:180, minWidth:180}">OrderUserName</th>
            <th lay-data="{field:'order_user_contact', minWidth:180}">OrderUserPhone</th>
            <th lay-data="{field:'order_user_address', minWidth:180}">OrderUserAddress</th>
            <th lay-data="{field:'order_status', minWidth:170, event:'updateStatus'}">Status</th>
            <th lay-data="{field:'order_price', minWidth:120}">OrderPrice</th>
            <th lay-data="{field:'order_shop_price', minWidth:120}">ShopPrice</th>
            <th lay-data="{field:'comment', minWidth:160, edit:'textarea'}">Comment</th>
            <th lay-data="{field:'order_time', minWidth:180}">OrderTimeConsuming</th>
            <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
            <th lay-data="{field:'order_created_at', minWidth:170}">CreatedAt</th>
            <th lay-data="{field:'order_updated_at', minWidth:170}">UpdatedAt</th>
            <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{$order->order_id}}</td>
                <td><span class="layui-btn layui-btn-sm @if($order->status==1) layui-bg-green @elseif($order->status==2) layui-bg-gray @else layui-btn-warm @endif">
                        @if($order->status==1) Completed @elseif($order->status==2) Canceled @else InProcess @endif</span>
                </td>
                <td>@if(!empty($order->shop->user_nick_name)){{$order->shop->user_nick_name}}@endif</td>
                <td>@if(!empty($order->shop->user_contact)){{$order->shop->user_contact}}@endif</td>
                <td>@if(!empty($order->shop->user_address)){{$order->shop->user_address}}@endif</td>
                <td>{{$order->user_name}}</td>
                <td>@if(!empty($order->user_contact)){{$order->user_contact}}@endif</td>
                <td>{{$order->user_address}}</td>
                <td>
                    <span class="layui-bg-{{$colorStyle[$order->schedule]}} layui-btn layui-btn-sm ">{{$schedule[$order->schedule]}}</span>
                </td>
                <td>@if(!empty($order->order_price)){{$order->order_price}}@endif</td>
                <td>@if(!empty($order->shop_price)){{$order->shop_price}}@endif</td>
                <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                <td>@if(!empty($order->color)){{$order->color}}@endif</td>
                <td>{{$order->created_at}}</td>
                <td>{{$order->updated_at}}</td>
                <td></td>
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
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            layuiTableColumnSelect: '/lay/modules/admin/table-select/js/layui-table-select'
        }).use(['common', 'table' , 'dropdown', 'layer' , 'layuiTableColumnSelect'], function () {
            const form = layui.form,
                dropdown = layui.dropdown,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                layuiTableColumnSelect = layui.layuiTableColumnSelect,
                $ = layui.jquery;
            table.init('table', { //转化静态表格
                page:false,
                limit:{{$perPage??10}},
                toolbar: '#toolbar',
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

            $('.select').on('change', function() {
                var params = {'status':$(this).val(), 'id':$(this).attr('data'), 'version':1};
                common.ajax("{{url('/backstage/business/discovery/order')}}", params, function(res){
                    location.reload();
                }, 'patch');
            });

            table.on('tool(table)', function (obj) {
                var data = obj.data;
                if (obj.event === 'goods') {
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%', '80%'],
                        offset: 'auto',
                        content: '/backstage/business/order/'+data.id,
                    });
                }
                if (obj.event ==='updateStatus') {
                    dropdown.render({
                        elem: this
                        ,show: true
                        ,data: @json($statusKv)
                        ,click: function(obj){
                            var params = {'status':obj.id, 'id':data.id, 'version':1};
                            common.ajax("{{url('/backstage/business/discovery/order')}}", params, function(res){
                                location.reload();
                            }, 'patch');
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
                @if(!Auth::user()->can('business::discovery.order.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                params['id']  = data.id;
                params['version'] = true;
                if (field==='order_price') {
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
                    common.ajax("{{url('/backstage/business/discovery/order')}}", params , function(res){
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
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="goods">Goods</a>
    </script>
@endsection