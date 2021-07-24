@extends('layouts.app')
    <style>
        textarea.layui-textarea.layui-table-edit {min-width: 300px; min-height: 200px; z-index: 9999999999 !important;}
        table tr td select {border: none; height: 29px;}
        table tr td select option{ color: #000000; background: #FFFFFF;}
        .layui-table-box .layui-table-header .layui-table thead tr th[data-field="total_price"] div span {
            text-decoration:line-through
        }
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop_order.shop')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="user_id" lay-filter="change">
                            <option value="0" @if(isset($user_id) && $user_id==0)  selected @endif>{{trans('business.table.header.shop_order.All')}}</option>
                            @foreach($shops as $shop)
                            <option value="{{$shop->user_id}}" @if(isset($user_id) && $user_id==$shop->user_id)  selected @endif>{{$shop->user_nick_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop_order.status')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="status"  lay-filter="change">
                            <option value="" @if(isset($status) && $status=='')  selected @endif>{{trans('business.table.header.shop_order.All')}}</option>
                            @foreach($orderStatuses as $key=>$value)
                                <option value="{{$key}}" @if(isset($status) && $status==$key)  selected @endif>{{trans('business.table.header.shop_order.'.$value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop_order.schedule')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="schedule"  lay-filter="change">
                            <option value="" @if(isset($schedule) && $schedule=='')  selected @endif>{{trans('business.table.header.shop_order.All')}}</option>
                            @foreach($schedules as $key=>$value)
                                <option value="{{$key}}" @if(isset($schedule) && $schedule==$key)  selected @endif>{{trans('business.table.header.shop_order.'.$value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" >{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
        <thead>
        <tr>
            <th lay-data="{fixed: 'left', field:'id', width:180}">{{trans('business.table.header.order.order_id')}}</th>
            <th lay-data="{field:'status', minWidth:120}">{{trans('business.table.header.order.status')}}</th>
            <th lay-data="{field:'shop_name', minWidth:180}">{{trans('business.table.header.shop.user_name')}}</th>
            <th lay-data="{field:'shop_contact', minWidth:180}">{{trans('business.table.header.shop.user_contact')}}</th>
            <th lay-data="{field:'shop_address', minWidth:180}">{{trans('business.table.header.shop.user_address')}}</th>
            <th lay-data="{field:'user_name', maxWidth:180, minWidth:180}">{{trans('business.table.header.order.user_name')}}</th>
            <th lay-data="{field:'user_contact', minWidth:180}">{{trans('business.table.header.order.user_contact')}}</th>
            <th lay-data="{field:'user_address', minWidth:180}">{{trans('business.table.header.order.user_address')}}</th>
            <th lay-data="{field:'schedule', minWidth:170, event:'updateStatus'}">{{trans('business.table.header.order.schedule')}}</th>
            <th lay-data="{field:'promo_code', minWidth:120}">{{trans('business.table.header.order.promo_code')}}</th>
            <th lay-data="{field:'delivery_coast', minWidth:120, edit:'text'}">{{trans('business.table.header.order.delivery_coast')}}</th>
            <th lay-data="{field:'discount_type', minWidth:120}">{{trans('business.table.header.order.discount_type')}}</th>
            <th lay-data="{field:'reduction', minWidth:120, edit:'text'}">{{trans('business.table.header.order.reduction')}}</th>
            <th lay-data="{field:'discount', minWidth:120, edit:'text'}">{{trans('business.table.header.order.discount')}}</th>
            <th lay-data="{field:'brokerage_percentage', minWidth:120, edit:'text'}">{{trans('business.table.header.order.brokerage_percentage')}}</th>
            <th lay-data="{field:'brokerage', minWidth:120}">{{trans('business.table.header.order.brokerage')}}
            <th lay-data="{field:'free_delivery', minWidth:100}">{{trans('business.table.header.order.free_delivery')}}</th>
            <th lay-data="{field:'comment', minWidth:160, edit:'textarea'}">{{trans('business.table.header.order.comment')}}</th>
            <th lay-data="{field:'goods', minWidth:200}">{{trans('business.table.header.order.goods')}}</th>
            <th lay-data="{field:'order_price', minWidth:150}">{{trans('business.table.header.order.order_price')}}</th>
            <th lay-data="{field:'promo_price', minWidth:150}">{{trans('business.table.header.order.promo_price')}}</th>
            <th lay-data="{field:'total_price', minWidth:150}" >{{trans('business.table.header.order.total_price')}}</th>
            <th lay-data="{field:'discounted_price', minWidth:150}">{{trans('business.table.header.order.discounted_price')}}</th>
            <th lay-data="{field:'profit', minWidth:150}">{{trans('business.table.header.order.profit')}}</th>
            <th lay-data="{field:'order_time', minWidth:180}">{{trans('business.table.header.order.order_time_consuming')}}</th>
            <th lay-data="{field:'color', maxWidth:1, hide:'true'}"></th>
            <th lay-data="{field:'delivered_at', minWidth:170}">{{trans('business.table.header.order.delivered_at')}}</th>
            <th lay-data="{field:'order_created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
            <th lay-data="{field:'order_updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
            <th lay-data="{fixed: 'right', minWidth:80, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{$order->order_id}}</td>
                <td>
                    <span class="layui-btn layui-btn-sm @if($order->status==1) layui-bg-green @elseif($order->status==2) layui-bg-gray @else layui-btn-warm @endif">
                        @if($order->status==1) {{trans('business.table.header.shop_order.Completed')}} @elseif($order->status==2) {{trans('business.table.header.shop_order.Canceled')}} @else {{trans('business.table.header.shop_order.InProcess')}} @endif
                    </span>
                </td>
                <td>@if(!empty($order->shop->user_nick_name)){{$order->shop->user_nick_name}}@endif</td>
                <td>@if(!empty($order->shop->user_contact)){{$order->shop->user_contact}}@endif</td>
                <td>@if(!empty($order->shop->user_address)){{$order->shop->user_address}}@endif</td>
                <td>{{$order->user_name}}</td>
                <td>@if(!empty($order->user_contact)){{$order->user_contact}}@endif</td>
                <td>{{$order->user_address}}</td>
                <td>
                    <span class="layui-bg-{{$colorStyles[$order->schedule]}} layui-btn layui-btn-sm ">{{trans('business.table.header.shop_order.'.$schedules[$order->schedule])}}</span>
                </td>
                <td>{{$order->promo_code}}</td>
                <td>{{$order->delivery_coast}}</td>
                <td>{{$order->discount_type}}</td>
                <td>{{$order->reduction}}</td>
                <td>{{$order->discount}}</td>
                <td>{{$order->brokerage_percentage}}</td>
                <td>@if(!empty($order->brokerage)){{$order->brokerage}}@endif</td>
                <td><input type="checkbox" @if($order->free_delivery==true) checked @endif name="free_delivery" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                <td>@if(!empty($order->comment)){{$order->comment}}@endif</td>
                <td>
                    @foreach($order->detail as $goods)
                        {{$goods['name']??''}}*{{$goods['goodsNumber']??0}}*{{$goods['price']??0}} /
                    @endforeach
                </td>
                <td>{{$order->order_price}}</td>
                <td>{{$order->promo_price}}</td>
                <td>{{$order->total_price}}</td>
                <td>{{$order->discounted_price}}</td>
                <td>{{$order->profit}}</td>
                <td>@if(!empty($order->order_time)){{$order->order_time}}mins @endif</td>
                <td>@if(!empty($order->color)){{$order->color}}@endif</td>
                <td>{{$order->delivered_at}}</td>
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
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker'
        }).use(['common', 'table' , 'dropdown', 'layer' , 'timePicker'], function () {
            const form = layui.form,
                dropdown = layui.dropdown,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.init('table', { //转化静态表格
                page:false,
                height: 'full-200',
                limit:{{$perPage}},
                totalRow: {
                    "order_id": 'Total',
                    "delivery_coast": {{$deliveryCoast}},
                    "order_price": {{$orderPrice}},
                    "promo_price": {{$promoPrice}},
                    "total_price": {{$totalPrice}},
                    "discounted_price": {{$discountedPrice}},
                    "reduction": {{$reductionCoast}},
                    "brokerage": {{$brokerageCoast}},
                    "profit": {{$profit}}
                },
                done: function(res, curr, count){
                    $('th').css({'font-size': '15'});	//进行表头样式设置
                    for(var i in res.data){		//遍历整个表格数据
                        var item = res.data[i];		//获取当前行数据
                        if(item.color==1){
                            $("tr[data-index='" + i + "']").attr({"style":"background:#ff3333; color:#fff"});  //将当前行变成绿色
                        }
                    }
                }
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}"
                },
            });

            table.on('tool(table)', function (obj) {
                let  selector = obj.tr.selector,data = obj.data;
                if (obj.event === 'detail') {
                    common.open_page('/backstage/business/shop_order/'+data.id);
                }else if(obj.event ==='updateStatus') {
                    @if(!Auth::user()->can('business::shop_order.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(".layui-table-box "+selector+" td[data-field=schedule]"));
                    table.render();
                    return true;
                    @endif
                    dropdown.render({
                        elem: this
                        ,show: true
                        ,data: @json($statusKv)
                        ,click: function(obj){
                            var params = {'schedule':obj.id};
                            common.confirm("{{trans('common.confirm.update')}}" , function(){
                                common.ajax("{{url('/backstage/business/shop_order')}}/"+data.id, params, function(res){
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
                @if(!Auth::user()->can('business::shop_order.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/shop_order')}}/"+data.id, params , function(res){
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

            form.on('switch(switchAll)', function(data){
                let checked = data.elem.checked;
                data.elem.checked = !checked;
                const name = $(data.elem).attr('name');
                let id = data.othis.parents('tr').find("td :first").text();
                const url = "{{url('/backstage/business/shop_order')}}/"+id;
                var params = {};
                if(checked) {
                    params[name] = "on";
                }else {
                    params[name] = "off";
                }
                @if(!Auth::user()->can('business::shop_order.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax(url , params , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt(res.result , 1 , 300 , 6 , 't');
                    } , 'patch' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });
            form.on('select(change)', function(data){
                console.log(data.elem); //得到select原始DOM对象
                console.log(data.value); //得到被选中的值
                console.log(data.othis); //得到美化后的DOM对象
                window.location = '?schedule='+$("select[name=schedule]").val()+'&user_id='+$('select[name=user_id]').val()+'&status='+$('select[name=status]').val()+"&dateTime="+$("#dateTime").val();
            });
        });
    </script>
    <script type="text/html" id="op">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        </div>
    </script>
@endsection