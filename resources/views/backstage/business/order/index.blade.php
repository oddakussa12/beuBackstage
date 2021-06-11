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
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type={{$type}}&user_id=0" class="layui-btn @if(isset($userId)&&$userId==0) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">All</a>
                        @foreach($shops as $shop)
                            <a href="?type={{$type}}&user_id={{$shop->user_id}}" class="layui-btn @if(isset($userId)&&$userId==$shop->user_id) layui-btn-disabled @else layui-btn-warm @endif  layui-btn-sm" target="_self">{{$shop->user_nick_name}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-btn-group">
                        <a href="?type=0&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='0') layui-btn-disabled @else layui-btn-normal @endif" target="_self">All</a>
                        <a href="?type=1&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='1') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Ordered</a>
                        <a href="?type=2&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='2') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ConfirmOrder</a>
                        <a href="?type=3&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='3') layui-btn-disabled @else layui-btn-normal @endif" target="_self">CalledDriver</a>
                        <a href="?type=4&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='4') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ContactedShop</a>
                        <a href="?type=5&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='5') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Delivered</a>
                        <a href="?type=6&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='6') layui-btn-disabled @else layui-btn-normal @endif" target="_self">NoResponse</a>
                        <a href="?type=7&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='7') layui-btn-disabled @else layui-btn-normal @endif" target="_self">JunkOrder</a>
                        <a href="?type=8&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='8') layui-btn-disabled @else layui-btn-normal @endif" target="_self">UserCancelOrder</a>
                        <a href="?type=9&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='9') layui-btn-disabled @else layui-btn-normal @endif" target="_self">ShopCancelOrder</a>
                        <a href="?type=10&user_id={{$userId}}" class="layui-btn @if(isset($type)&&$type=='10') layui-btn-disabled @else layui-btn-normal @endif" target="_self">Other</a>
                    </div>
                </div>
            </div>

        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', width:180 , fixed:'left'}">OrderId</th>
                <th lay-data="{field:'shop', minWidth:180}">Shop</th>
                <th lay-data="{field:'shop_contact', maxWidth:100}">ShopContact</th>
                <th lay-data="{field:'shop_address', maxWidth:100}">ShopAddress</th>
                <th lay-data="{field:'goods_name', maxWidth:100}">GoodsName</th>
                <th lay-data="{field:'order_user_name', maxWidth:180, minWidth:120}">OrderUserName</th>
                <th lay-data="{field:'order_user_contact', maxWidth:180, minWidth:120}">OrderUserContact</th>
                <th lay-data="{field:'order_user_address', maxWidth:180, minWidth:120}">OrderUserAddress</th>
                <th lay-data="{field:'order_status', maxWidth:180, minWidth:150, templet: function(field){
                    var selectParams = {
                        '1':'Ordered',
                        '2':'ConfirmOrder',
                        '3':'CalledDriver',
                        '4':'ContactedShop',
                        '5':'Delivered',
                        '6':'NoResponse',
                        '7':'JunkOrder',
                        '8':'UserCancelOrder',
                        '9':'ShopCancelOrder',
                        '10':'Other'
                    };
                    var status = field.order_status;
                    if(status==1)
                    {
                        var c = 'layui-badge-rim';
                    }else if(status==2)
                    {
                        var c = 'layui-bg-yellow';
                    }else if(status==3)
                    {
                        var c = 'layui-bg-orange';
                    }else if(status==4)
                    {
                        var c = 'layui-bg-pink';
                    }else if(status==5)
                    {
                        var c = 'llayui-bg-green';
                    }else if(status==6)
                    {
                        var c = 'layui-bg-blue';
                    }else if(status==7)
                    {
                        var c = 'layui-bg-orange';
                    }else if(status==8||status==9||status==10)
                    {
                        var c = 'layui-bg-gray';
                    }else{
                        var c = '';
                    }
                    console.log(c);
                    return '<span class=\'layui-badge '+c+'\'>'+selectParams[field.order_status]+'</span>';

                },event:'updateStatus'}">Status</th>
                <th lay-data="{field:'comment', minWidth:160, edit:'textarea'}">Comment</th>

                <th lay-data="{field:'order_created_at', maxWidth:180, minWidth:100}">CreatedAt</th>
                <th lay-data="{field:'order_updated_at', maxWidth:180, minWidth:100}">UpdatedAt</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->order_id}}</td>
                    <td>{{$order->owner->user_nick_name}}</td>
                    <td>{{$order->owner->user_contact}}</td>
                    <td>{{$order->owner->user_address}}</td>
                    <td>{{empty($order->g)?'':$order->g->name}}</td>
                    <td>{{$order->user_name}}</td>
                    <td>{{$order->user_contact}}</td>
                    <td>{{$order->user_address}}</td>
                    <td>{{$order->status}}</td>
                    <td>{{$order->comment}}</td>
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
            common: 'lay/modules/admin/common',
            layuiTableColumnSelect: '/lay/modules/admin/table-select/js/layui-table-select'
        }).use(['common' , 'table' , 'layer' , 'layuiTableColumnSelect'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                layuiTableColumnSelect = layui.layuiTableColumnSelect,
                $ = layui.jquery;
            var order = table.init('table', { //转化静态表格
                page:false
                ,height: '700px;'
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
            var selectParams = [
                {name:'1',value:'Ordered'},
                {name:'2',value:'ConfirmOrder'},
                {name:'3',value:'CalledDriver'},
                {name:'4',value:'ContactedShop'},
                {name:'5',value:'Delivered'},
                {name:'6',value:'NoResponse'},
                {name:'7',value:'JunkOrder'},
                {name:'8',value:'UserCancelOrder'},
                {name:'9',value:'ShopCancelOrder'},
                {name:'10',value:'Other'},
            ];
            layuiTableColumnSelect.addSelect({data:selectParams,layFilter:'table',event:'updateStatus',field:'order_status',callback:function(obj,update){
                    var params = {'status':update.order_status , 'id':obj.data.id};
                    common.ajax("{{url('/backstage/business/discovery/order')}}", params, function(res){
                        obj.update(update);
                        parent.location.reload();
                    } , 'patch');
                }});
            form.render();
            setTimeout(function() {
                location.reload();
            }, 60000);
        });
    </script>
@endsection