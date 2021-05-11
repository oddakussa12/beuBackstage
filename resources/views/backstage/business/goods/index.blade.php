@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">ShopName:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="shopName" placeholder="shop name" id="shopName" @if(!empty($shopName)) value="{{$shopName}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">GoodsName:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" placeholder="goods name" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Sort:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="created_at">CreatedAt</option>
                            <option value="like" @if(isset($sort) && $sort=='like') selected @endif>Liked</option>
                            <option value="price" @if(isset($sort) && $sort=='price') selected @endif>Price</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Recommend:</label>
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
                <th lay-data="{field:'id', minWidth:180}">GoodId</th>
                <th lay-data="{field:'shop_name', minWidth:160}">ShopName</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">ShopNickName</th>
                <th lay-data="{field:'name', minWidth:160}">GoodsName</th>
                <th lay-data="{field:'image', minWidth:200}">Image</th>
                <th lay-data="{field:'like', minWidth:100}">Like</th>
                <th lay-data="{field:'price', minWidth:100}">Price</th>
                <th lay-data="{field:'recommend', minWidth:120}">Recommend</th>
                <th lay-data="{field:'recommended_at', minWidth:160}">RecommendTime</th>
                <th lay-data="{field:'status', minWidth:100}">InStock</th>
                <th lay-data="{field:'description', minWidth:200}">Description</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
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
                    <td>{{$value->price}}{{$value->currency}}</td>
                    <td><input type="checkbox" @if($value->recommend==1) checked @endif name="recommend" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->recommended_at}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(empty($value->status)) layui-btn-danger @else layui-btn-warm @endif">@if(empty($value->status)) NO @else YES @endif</span></td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->created_at}}</td>
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
            $(function () {
                hoverOpenImg();
            });
            function  hoverOpenImg(){
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this,{
                        tips:1,
                    });
                },function(){
                    // layer.close(img_show);
                });
                //$('td img').attr('style','max-width:400px');
            }
        });

    </script>
    <script type="text/html" id="postop">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="comment">Comment</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">{{trans('common.table.button.delete')}}</a>
    </script>
@endsection