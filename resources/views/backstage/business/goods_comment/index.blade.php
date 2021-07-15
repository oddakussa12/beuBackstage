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
                    <label class="layui-form-label">{{trans('business.form.label.goods_comment.goods_id')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="goods_id" placeholder="{{trans('business.form.placeholder.goods.id')}}" id="goods_id" @if(!empty($goods_id)) value="{{$goods_id}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="order_by">
                            @foreach(trans('business.form.select.goods_comment.order_by') as $k=>$v)
                            <option value="{{$k}}" @if(isset($order_by) && $order_by==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods_comment.level')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="level">
                            @foreach(trans('business.form.select.goods_comment.level') as $k=>$v)
                                <option value="{{$k}}" @if(isset($level) && $level==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.table.header.verified')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="verified">
                            @foreach(trans('business.form.select.goods_comment.verified') as $k=>$v)
                                <option value="{{$k}}" @if(isset($verified) && $verified==$k) selected @endif>{{$v}}</option>
                            @endforeach
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
                <th lay-data="{field:'comment_id', minWidth:180, hide:'true', fixed:'left'}">{{trans('business.table.header.goods_comment.id')}}</th>
                <th lay-data="{field:'shop_nick_name', minWidth:160, fixed:'left'}">{{trans('business.table.header.shop.user_name')}}</th>
                <th lay-data="{field:'goods_name', minWidth:160}">{{trans('business.table.header.goods.name')}}</th>
                <th lay-data="{field:'level', minWidth:100}">{{trans('business.table.header.goods_comment.level')}}</th>
                <th lay-data="{field:'status', minWidth:110}">{{trans('business.table.header.goods_comment.status')}}</th>
                <th lay-data="{field:'verified', minWidth:150 , templet: function(field){
                    if(field.verified==1)
                    {
                        var str = '<div id='+field.comment_id+'><input type=\'radio\' disabled name='+field.comment_id+' field=\'verified\' checked lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'verified\' disabled name='+field.comment_id+' lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }else if(field.verified==0)
                    {
                        var str = '<div id='+field.comment_id+'><input type=\'radio\' disabled name='+field.comment_id+' field=\'verified\' lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'verified\' disabled name='+field.comment_id+' checked lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }else{
                        var str = '<div id='+field.comment_id+'><input type=\'radio\' name='+field.comment_id+' field=\'verified\' lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'verified\' name='+field.comment_id+' lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }
                    return str; }}">{{trans('business.table.header.goods_comment.verified')}}</th>
                <th lay-data="{field:'point', minWidth:100}">{{trans('business.table.header.goods_comment.point')}}</th>
                <th lay-data="{field:'quality', minWidth:100}">{{trans('business.table.header.goods_comment.quality')}}</th>
                <th lay-data="{field:'service', minWidth:100}">{{trans('business.table.header.goods_comment.service')}}</th>

                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.goods_comment.comment_user')}}</th>
                <th lay-data="{field:'media', minWidth:160}">{{trans('business.table.header.goods_comment.media')}}</th>
                <th lay-data="{field:'content', minWidth:200}">{{trans('business.table.header.goods_comment.content')}}</th>
                <th lay-data="{field:'to_id', minWidth:120}">{{trans('business.table.header.goods_comment.to_user')}}</th>

                <th lay-data="{field:'child_comment', minWidth:100}">{{trans('business.table.header.goods_comment.child_comment')}}</th>
                <th lay-data="{field:'verified_at', minWidth:160}">{{trans('business.table.header.goods_comment.verified_at')}}</th>

                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($comments as $value)
                <tr>
                    <td>{{$value->comment_id}}</td>
                    <td>@if(!empty($value->shop->user_nick_name)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/shop')}}?keyword={{$value->shop->user_nick_name}}">{{$value->shop->user_nick_name}}</a>@endif</td>
                    <td>@if(!empty($value->goods->name)) <a target="_blank" style="color: #FFB800" href="{{url('/backstage/business/goods')}}?goods_id={{$value->goods->id}}&keyword={{$value->goods->name}}">{{$value->goods->name}}</a>@endif</td>
                    <td><input type="checkbox" @if($value->level==1) checked @endif name="level" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><span class="layui-btn layui-btn-xs @if($value->verified==-1) layui-btn-danger @elseif($value->verified==0) layui-btn-warm @else layui-btn-normal @endif">
                            @if($value->verified==1) Pass @elseif($value->verified==0) Refuse @else UnAudited @endif
                        </span>
                    </td>
                    <td>{{$value->verified}}</td>
                    <td>{{$value->point}}</td>
                    <td>{{$value->quality}}</td>
                    <td>{{$value->service}}</td>
                    <td>@if(!empty($value->user->user_nick_name))<a target="_blank" style="color: #FFB800" href="{{url('/backstage/passport/user')}}?keyword={{$value->user->user_nick_name}}">{{$value->user->user_nick_name}}</a>@endif</td>
                    <td>
                        @if(!empty($value->media))
                            @foreach($value->media as $image)
                                @if(stripos($image['url'], 'video'))
                                    <video controls="controls" autoplay="autoplay" width="100%" height="380px"><source src="{{$value->media}}" type="video/mp4" /></video>
                                @else
                                <img src="{{$image['url']}}">
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{$value->content}}</td>
                    <td>@if(!empty($value->to->user_name))<a target="_blank" style="color: #FFB800" href="{{url('/backstage/passport/user')}}?keyword={{$value->to->user_name}}">{{$value->to->user_name}}</a>@endif</td>
                    <td>@if(!empty($value->child_comment)){{$value->child_comment}}@else 0 @endif</td>
                    <td>@if($value->verified_at!='0000-00-00 00:00:00'){{$value->verified_at}}@endif</td>
                    <td>{{$value->created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $comments->links('vendor.pagination.default') }}
        @else
            {{ $comments->appends($appends)->links('vendor.pagination.default') }}
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
            form.on('radio(radio)', function(data){
                let value  = data.value;
                let t = data.type;
                let elem   = data.elem;
                let that = this;
                let thatVal = $(that).val();
                let id = data.othis.parent().attr('id');
                console.log(data.othis.parent());
                let n = $(elem).attr('name');
                @if(!Auth::user()->can('business::goods_comment.update'))
                $("div[lay-id=table] input[name="+n+"]").each(function(e){
                    if(this.defaultChecked)
                    {
                        $(this).prop("checked",true);
                    }else{
                        $(this).prop("checked",false);
                    }
                });
                form.render();
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", '#'+id);
                return false;
                @endif;
                $("div[lay-id=table] input[name="+n+"]").each(function(e){
                    if(this.defaultChecked)
                    {
                        $(this).prop("checked",true);
                    }else{
                        $(this).prop("checked",false);
                    }
                });
                form.render();
                let field = $(that).attr('field');
                let params = {};
                params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/goods_comment')}}/"+id, params , function(res){
                        $(that).prop("checked",true);
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        location.reload();
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            console.log(n);
                            $("div[lay-id=table] input[name="+n+"]").each(function(e){
                                if(this.defaultChecked)
                                {
                                    $(this).prop("checked",true);
                                }else{
                                    $(this).prop("checked",false);
                                }
                            });
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
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
                @if(!Auth::user()->can('business::goods_comment.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/goods_comment')}}/"+data.id, JSON.parse(params) , function(res){
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
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                console.log(data);
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
@endsection