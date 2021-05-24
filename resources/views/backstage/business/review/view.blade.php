@extends('layouts.app')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
        .layui-layer-content { display: flex; align-items: center; justify-content: center; text-align: justify; margin:0 auto; }

    </style>
    <div  class="layui-fluid">
        <br>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="desc" @if(isset($sort) && $sort=='desc') selected @endif>Desc</option>
                            <option value="asc" @if(isset($sort) && $sort=='asc') selected @endif>Asc</option>
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
                <th lay-data="{field:'comment_id', minWidth:180}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'goods_name', minWidth:160}">{{trans('business.table.header.goods_name')}}</th>
                <th lay-data="{field:'image', minWidth:160}">{{trans('common.table.header.image')}}</th>

                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.comment_user')}}</th>
                <th lay-data="{field:'media', minWidth:160}">{{trans('business.table.header.media')}}</th>
                <th lay-data="{field:'content', minWidth:200}">{{trans('business.table.header.content')}}</th>
                <th lay-data="{field:'to_id', minWidth:120}">{{trans('business.table.header.to_user')}}</th>
                <th lay-data="{field:'top_id', minWidth:120}">{{trans('business.table.header.top_user')}}</th>

                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', width:200, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->comment_id}}</td>
                    <td>@if(!empty($value->shop_nick_name)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/shop')}}?keyword={{$value->shop_nick_name}}">{{$value->shop_nick_name}}</a>@endif</td>
                    <td>@if(!empty($value->goods_name)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/goods')}}?goods_id={{$value->goods_id}}&keyword={{$value->goods_name}}">{{$value->goods_name}}</a>@endif
                    </td>
                    <td>@if(!empty($value->image))
                            @foreach($value->image as $image)
                                <img src="{{$image['url']}}">
                            @endforeach
                        @endif
                    </td>
                    <td>@if(!empty($value->user_nick_name))<a target="_blank" style="color: #FFB800" href="{{url('/backstage/passport/user')}}?keyword={{$value->user_nick_name}}">{{$value->user_nick_name}}</a>@endif</td>

                    <td>
                        @if(!empty($value->media))
                            @foreach($value->media as $image)
                                @if(stripos($image['url'], '.mp4'))
                                    <video controls="controls" autoplay="autoplay" width="100%" height="380px"><source src="{{$value->media}}" type="video/mp4" /></video>
                                @else
                                    <img src="{{$image['url']}}">
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{$value->content}}</td>
                    <td>@if(!empty($value->to_nick_name))<a target="_blank" style="color: #FFB800" href="{{url('/backstage/passport/user')}}?keyword={{$value->to_nick_name}}">{{$value->to_nick_name}}</a>@endif</td>
                    <td>@if(!empty($value->top_nick_name))<a target="_blank" style="color: #FFB800" href="{{url('/backstage/passport/user')}}?keyword={{$value->top_nick_name}}">{{$value->top_nick_name}}</a>@endif</td>
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
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'media'){
                    data.media = $.trim(data.media);
                    let area = data.media!=='' && data.media!==undefined ? ['95%','95%'] : ['40%','30%'];
                    let content = data.media!=='' && data.media!==undefined ? data.media : data.content;

                    if(data.media.indexOf('video')!==-1){
                        area = ['50%','60%'];
                    }
                    layer.open({
                        type: 1,
                        shadeClose: true,
                        shade: 0.8,
                        area: area,
                        offset: 'auto',
                        scrollbar:true,
                        content: content,
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
        <a class="layui-btn layui-btn-xs" lay-event="media">{{trans('business.table.header.media')}}</a>
    </script>
@endsection