@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
         .layui-layer-content { display: flex; align-items: center; justify-content: center; text-align: justify; margin:0 auto; }
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
                <div class="layui-inline" style="display: none">
                    <label class="layui-form-label">{{trans('business.table.header.verified')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="verify">
                            <option value="-1" @if(isset($verify) && $verify=='-1') selected @endif>UnAudited</option>
                            <option value="">All</option>
                            <option value="1" @if(isset($verify) && $verify=='1') selected @endif>Pass</option>
                            <option value="0" @if(isset($verify) && $verify=='0') selected @endif>Refuse</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline ">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline">
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
                <th lay-data="{field:'comment_id', minWidth:180, hide:'true'}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'goods_name', minWidth:160}">{{trans('business.table.header.goods_name')}}</th>
                <th lay-data="{field:'image', minWidth:160}">{{trans('common.table.header.image')}}</th>
                <th lay-data="{field:'level', minWidth:100}">{{trans('business.table.header.recommend')}}</th>
                <th lay-data="{field:'verifiedd', minWidth:110}">{{trans('common.table.header.status')}}</th>
                <th lay-data="{field:'verified', minWidth:160}">{{trans('business.table.header.verified')}}</th>

                <th lay-data="{field:'point', minWidth:100}">{{trans('business.table.header.shop_score')}}</th>
                <th lay-data="{field:'service', minWidth:100}">{{trans('business.table.header.service')}}</th>
                <th lay-data="{field:'quality', minWidth:100}">{{trans('business.table.header.quality')}}</th>

                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.comment_user')}}</th>
                <th lay-data="{field:'media', minWidth:160}">{{trans('business.table.header.media')}}</th>
                <th lay-data="{field:'content', minWidth:200}">{{trans('business.table.header.content')}}</th>

                <th lay-data="{field:'verified_at', minWidth:160}">{{trans('business.table.header.verified_at')}}</th>

                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', width:200, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->comment_id}}</td>
                    <td>@if(!empty($value->shop_nick_name)){{$value->shop_nick_name}}@endif</td>
                    <td>@if(!empty($value->goods_name)){{$value->goods_name}}@endif
                    </td>
                    <td>@if(!empty($value->image))
                            @foreach($value->image as $image)
                                <img src="{{$image['url']}}">
                            @endforeach
                        @endif
                    </td>
                    <td><input type="checkbox" @if($value->level==1) checked @endif name="level" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><span class="layui-btn layui-btn-xs @if($value->verified==-1) layui-btn-danger @elseif($value->verified==0) layui-btn-warm @else layui-btn-normal @endif">
                            @if($value->verified==1) Pass @elseif($value->verified==0) Refuse @else UnAudited @endif
                        </span>
                    </td>
                    <td>
                        <input type="radio" name="audit_{{$value->comment_id}}" @if($value->verified==1) checked @endif lay-filter="radio" value="pass" title="Pass">
                        <input type="radio" name="audit_{{$value->comment_id}}" @if($value->verified==0) checked @endif lay-filter="radio" value="refuse" title="Refuse">
                    </td>
                    <td>{{$value->service}}</td>
                    <td>{{$value->point}}</td>
                    <td>{{$value->quality}}</td>

                    <td>@if(!empty($value->user_nick_name)){{$value->user_nick_name}}@endif</td>

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
                    <td>@if($value->verified_at!='0000-00-00 00:00:00'){{$value->verified_at}}@endif</td>
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
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                },
            });
            form.on('radio(radio)', function(data){
                let level  = data.value;
                const name = 'audit';
                let params = '{"' + name + '":"'+level+'"}';
                data.id    = data.othis.parents('tr').find("td :first").text();
                request(data, params, name);
            });
            form.on('switch(switchAll)', function(data){
                let params;
                const checked = data.elem.checked;
                data.elem.checked = !checked;
                data.id = data.othis.parents('tr').find("td :first").text();
                const name = $(data.elem).attr('name');
                if(checked) {
                    params = '{"' + name + '":"on"}';
                }else {
                    params = '{"' + name + '":"off"}';
                }
                request(data, params, name, checked);
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
                        content: '/backstage/business/review/view/'+data.comment_id,
                    });
                }
                if(layEvent === 'media'){
                    data.media = $.trim(data.media);
                    let area = ['95%','95%'];
                    let content = data.media!=='' && data.media!==undefined ? data.media : data.content;
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
            function request(data, params, name, checked=false) {
                @if(!Auth::user()->can('business::review.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/review')}}/"+data.id, JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        name==='audit' && location.reload();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            }
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
        <a class="layui-btn layui-btn-xs" lay-event="view">{{trans('common.table.button.detail')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="media">{{trans('business.table.header.media')}}</a>
    </script>
@endsection