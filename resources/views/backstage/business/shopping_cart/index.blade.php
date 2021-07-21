@extends('layouts.app')
    <style>
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shopping_cart.shop_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="shop_name" placeholder="{{trans('business.table.header.shop_name')}}" id="shopName" @if(!empty($shop_name)) value="{{$shop_name}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shopping_cart.goods_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="goods_name" placeholder="{{trans('business.table.header.goods_name')}}" id="goods_name" @if(!empty($goods_name)) value="{{$goods_name}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shopping_cart.user_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input"  name="user_name" placeholder="{{trans('user.table.header.user_name')}}" id="userName" @if(!empty($user_name)) value="{{$user_name}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            @foreach(trans('business.form.select.shopping_cart') as $k=>$v)
                            <option value="{{$k}}" @if(isset($sort) && $sort==$k)  selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
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
                <th lay-data="{field:'id', minWidth:180, hide:'true'}"></th>
                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.shopping_cart.user_nick_name')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">{{trans('business.table.header.shopping_cart.shop_nick_name')}}</th>
                <th lay-data="{field:'goods_name', minWidth:160}">{{trans('business.table.header.shopping_cart.goods_name')}}</th>
                <th lay-data="{field:'image', minWidth:200}">{{trans('business.table.header.shopping_cart.goods_image')}}</th>
                <th lay-data="{field:'number', minWidth:200}">{{trans('business.table.header.shopping_cart.goods_number')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shoppingCarts as $shoppingCart)
                <tr>
                    <td>{{$shoppingCart->id}}</td>
                    <td>{{$shoppingCart->user_nick_name}}</td>
                    <td>{{$shoppingCart->shop_nick_name}}</td>
                    <td>{{$shoppingCart->goods_name}}</td>
                    <td>@if(!empty($shoppingCart->goods_image))
                            @foreach($shoppingCart->goods_image as $image)
                                <img src="{{$image['url']}}">
                            @endforeach
                        @endif
                    </td>
                    <td>{{$shoppingCart->number}}</td>
                    <td>{{$shoppingCart->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $shoppingCarts->links('vendor.pagination.default') }}
        @else
            {{ $shoppingCarts->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
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
            const layer = layui.layer,
                table = layui.table,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar',
                limit:{{$perPage}}
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}",
                },
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
@endsection