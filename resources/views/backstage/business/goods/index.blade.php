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
                    <label class="layui-form-label">{{trans('business.table.header.shop_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="shopName" placeholder="{{trans('business.table.header.shop_name')}}" id="shopName" @if(!empty($shopName)) value="{{$shopName}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.table.header.goods_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" placeholder="{{trans('business.table.header.goods_name')}}" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.table.header.goods_id')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" style="width: 300px" name="goods_id" placeholder="{{trans('business.table.header.goods_id')}}" id="goods_id" @if(!empty($goods_id)) value="{{$goods_id}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="created_at">CreatedAt</option>
                            <option value="like" @if(isset($sort) && $sort=='like') selected @endif>Liked</option>
                            <option value="price" @if(isset($sort) && $sort=='price') selected @endif>Price</option>
                            <option value="view_num" @if(isset($sort) && $sort=='view_num') selected @endif>ViewNum</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.table.header.recommend')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="recommend">
                            <option value="">All</option>
                            <option value="1" @if(isset($recommend) && $recommend=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($recommend) && $recommend=='0') selected @endif>NO</option>
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
                </div>
            </div>
        </form>

        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', minWidth:180}">{{trans('business.table.header.goods_id')}}</th>
                <th lay-data="{field:'shop_name', minWidth:160}">{{trans('business.table.header.shop_name')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'name', minWidth:160}">{{trans('business.table.header.goods_name')}}</th>
                <th lay-data="{field:'image', minWidth:200}">{{trans('common.table.header.image')}}</th>
                <th lay-data="{field:'like', minWidth:120}">{{trans('business.table.header.like')}}</th>
                <th lay-data="{field:'price', minWidth:120}">{{trans('business.table.header.price')}}</th>
                <th lay-data="{field:'view_num', minWidth:120}">{{trans('business.table.header.view_num')}}</th>
                <th lay-data="{field:'recommend', minWidth:120}">{{trans('business.table.header.recommend')}}</th>
                <th lay-data="{field:'recommended_at', minWidth:160}">{{trans('business.table.header.recommended_at')}}</th>
                <th lay-data="{field:'status', minWidth:100}">{{trans('business.table.header.in-stock')}}</th>
                <th lay-data="{field:'description', minWidth:200}">{{trans('common.table.header.description')}}</th>
                <th lay-data="{field:'score', minWidth:120}">{{trans('business.table.header.shop_score')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->shop_name}}</td>
                    <td>{{$value->shop_nick_name}}</td>
                    <td>{{$value->name}}</td>
                    <td>@if(!empty($value->image))
                            @foreach($value->image as $image)
                                <img src="{{$image['url']}}">
                            @endforeach
                        @endif
                    </td>
                    <td>{{$value->like}}</td>
                    <td>{{$value->price}} {{$value->currency}}</td>
                    <td>@if(!empty($value->view_num)){{$value->view_num}}@else 0 @endif</td>
                    <td><input type="checkbox" @if($value->recommend==1) checked @endif name="recommend" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>@if($value->recommended_at!='0000-00-00 00:00:00'){{$value->recommended_at}}@endif</td>
                    <td><span class="layui-btn layui-btn-xs @if(empty($value->status)) layui-btn-danger @else layui-btn-warm @endif">@if(empty($value->status)) NO @else YES @endif</span></td>
                    <td>{{$value->description}}</td>
                    <td>@if(!empty($value->score)){{$value->score}}@else 0 @endif</td>
                    <td>{{$value->created_at}}</td>
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
                    common.ajax("{{url('/backstage/business/goods')}}/"+id , JSON.parse(params) , function(res){
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
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%','95%'],
                        offset: 'auto',
                        scrollbar:true,
                        content: '/backstage/business/goods/view/'+data.id,
                    });
                }
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
        <a class="layui-btn layui-btn-xs" lay-event="view">{{trans('business.table.header.view_history')}}</a>
    </script>
@endsection