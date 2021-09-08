@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods.name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="goods_name" placeholder="{{trans('business.form.placeholder.goods.name')}}" id="goods_name" @if(!empty($goods_name)) value="{{$goods_name}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods.id')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input"  name="goods_id" placeholder="{{trans('business.form.placeholder.goods.id')}}" id="goods_id" @if(!empty($goods_id)) value="{{$goods_id}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            @foreach(trans('business.form.select.goods_sort') as $key=>$translation)
                                <option value="{{$key}}" @if(!empty($sort)&&$sort==$key) selected @endif>{{$translation}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods.recommendation')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="recommendation">
                            @foreach(trans('common.form.select.query') as $key=>$translation)
                                <option value="{{$key}}" @if(isset($recommendation)&&$recommendation===strval($key)) selected @endif>{{$translation}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
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
                <th lay-data="{field:'id', minWidth:300}">{{trans('business.table.header.goods.id')}}</th>
                <th lay-data="{field:'shop_name', minWidth:160}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">{{trans('business.table.header.shop.user_nick_name')}}</th>
                <th lay-data="{field:'name', minWidth:160}">{{trans('business.table.header.goods.name')}}</th>
                <th lay-data="{field:'category', minWidth:100}">{{trans('business.table.header.goods.category')}}</th>
                <th lay-data="{field:'image', minWidth:200}">{{trans('business.table.header.goods.image')}}</th>
                <th lay-data="{field:'price', minWidth:140}">{{trans('business.table.header.goods.price')}}</th>
                <th lay-data="{field:'packaging_cost', minWidth:140}">{{trans('business.table.header.goods.packaging_cost')}}</th>
                <th lay-data="{field:'total_price', minWidth:140}">{{trans('business.table.header.goods.total_price')}}</th>
                <th lay-data="{field:'purchase_price', minWidth:140,edit:'text'}">{{trans('business.table.header.goods.purchase_price')}}</th>
                <th lay-data="{field:'package_purchase_price', minWidth:160,edit:'text'}">{{trans('business.table.header.goods.package_purchase_price')}}</th>
                <th lay-data="{field:'total_purchase_price', minWidth:140}">{{trans('business.table.header.goods.total_purchase_price')}}</th>
                <th lay-data="{field:'recommendation', minWidth:140}">{{trans('business.table.header.goods.recommendation')}}</th>
                <th lay-data="{field:'charge', minWidth:100,edit:'text'}">{{trans('business.table.header.goods.charge')}}</th>
                <th lay-data="{field:'status', minWidth:100}">{{trans('business.table.header.goods.status')}}</th>
                <th lay-data="{field:'comment', minWidth:200}">{{trans('business.table.header.goods.comment')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:280, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($goods as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->shop->user_name}}</td>
                    <td>{{$value->shop->user_nick_name}}</td>
                    <td>{{$value->name}}</td>
                    <td>@if(!empty($value->category->name)) {{$value->category->name}} @endif</td>
                    <td>@if(!empty($value->image))
                            @foreach($value->image as $image)
                                <img src="{{$image['url']}}">
                            @endforeach
                        @endif
                    </td>
                    <td>{{$value->price}} {{$value->currency}}</td>
                    <td>{{$value->packaging_cost}}</td>
                    <td>{{$value->price+$value->packaging_cost}}</td>
                    <td>{{$value->purchase_price}}</td>
                    <td>{{$value->package_purchase_price}}</td>
                    <td>{{$value->purchase_price+$value->package_purchase_price}}</td>
                    <td><input type="checkbox" @if($value->recommend==1) checked @endif name="recommend" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->charge}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(empty($value->status)) layui-btn-danger @else layui-btn-warm @endif">@if(empty($value->status)) NO @else YES @endif</span></td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $goods->links('vendor.pagination.default') }}
        @else
            {{ $goods->appends($appends)->links('vendor.pagination.default') }}
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
                    locale:"{{locale()}}"
                },
            });
            form.on('switch(switchAll)', function(data){
                let params;
                const checked = data.elem.checked;
                const id = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('business::goods.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                const name = $(data.elem).attr('name');
                if(checked) {
                    params = '{"' + name + '":"on"}';
                }else {
                    params = '{"' + name + '":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{LaravelLocalization::localizeUrl('/backstage/business/goods')}}/"+id , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'view'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/goods')}}/"+data.id+'/view/');
                }else if(layEvent === 'comment'){
                    window.open("/backstage/business/goods_comment?goods_id="+data.id);
                }else if(layEvent === 'special'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/special_goods/create')}}"+"?goods_id="+data.id+"&timezone="+Intl.DateTimeFormat().resolvedOptions().timeZone);
                }else if(layEvent === 'delay'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/delay_special_goods/create')}}"+"?goods_id="+data.id+"&timezone="+Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
            });

            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value
                    ,data = obj.data
                    ,field = obj.field
                    ,original = $(this).prev().text(); //得到字段
                var params = {},d={};
                params[field] = value;
                d[field] = original;
                @if(!Auth::user()->can('business::goods.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/goods/')}}/"+data.id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        location.reload();
                    } , 'patch' , function (event,xhr,options,exc) {
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
            $(function () {
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
        });
    </script>
    <script type="text/html" id="op">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-xs" lay-event="view">{{trans('business.table.button.goods.view_history')}}</a>
            <a class="layui-btn layui-btn-xs" lay-event="comment">{{trans('business.table.button.goods.comment')}}</a>
            <a class="layui-btn layui-btn-xs" lay-event="special">{{trans('business.table.button.goods.special')}}</a>
            <a class="layui-btn layui-btn-xs" lay-event="delay">{{trans('business.table.button.goods.delay')}}</a>
        </div>
    </script>
@endsection