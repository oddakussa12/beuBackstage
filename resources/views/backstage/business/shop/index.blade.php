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
                    <label class="layui-form-label">{{trans('business.form.label.shop.user_name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_name" placeholder="{{trans('business.form.label.shop.user_name')}}" id="user_name" @if(!empty($user_name)) value="{{$user_name}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop.user_phone')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_phone" placeholder="{{trans('business.form.label.shop.user_phone')}}" id="user_phone" @if(!empty($user_phone)) value="{{$user_phone}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop.user_country')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="country_code" lay-verify="" lay-search  >
                            <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                            @foreach($countries  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop.user_verified')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="user_verified">
                            <option value="">ALL</option>
                            @foreach(trans('business.form.select.shop_review') as $k=>$v)
                            <option value="{{$k}}" @if(isset($user_verified) && $user_verified==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.shop.user_delivery')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="user_delivery">
                            @foreach(trans('business.form.select.user_delivery') as $k=>$v)
                                <option value="{{$k}}" @if(isset($user_delivery) && $user_delivery==$k) selected @endif>{{$v}}</option>
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
                <th lay-data="{field:'user_id', minWidth:130}">{{trans('business.table.header.shop_id')}}</th>
                <th lay-data="{field:'country', minWidth:80}">{{trans('user.form.label.user_country')}}</th>
                <th lay-data="{field:'user_name', minWidth:160}">{{trans('business.table.header.shop_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'user_status', minWidth:150}">{{trans('common.table.header.status')}}</th>
                <th lay-data="{field:'user_verified', minWidth:150 , templet: function(field){
                    if(field.user_verified==1)
                    {
                        var str = '<div id='+field.user_id+'><input type=\'radio\' name='+field.user_id+' field=\'user_verified\' checked lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'user_verified\' name='+field.user_id+' lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }else if(field.user_verified==0)
                    {
                        var str = '<div id='+field.user_id+'><input type=\'radio\' name='+field.user_id+' field=\'user_verified\' lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'user_verified\' name='+field.user_id+' checked lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }else{
                        var str = '<div id='+field.user_id+'><input type=\'radio\' name='+field.user_id+' field=\'user_verified\' lay-filter=\'radio\' value=\'yes\' title=\'YES\'><input type=\'radio\' field=\'user_verified\' name='+field.user_id+' lay-filter=\'radio\' value=\'no\' title=\'NO\'></div>';
                    }
                    return str; }}">{{trans('business.table.header.verified')}}</th>
                <th lay-data="{field:'user_delivery', minWidth:150}">{{trans('business.table.header.take_out')}}</th>
                <th lay-data="{field:'recommend', minWidth:120}">{{trans('business.table.header.recommend')}}</th>
                <th lay-data="{field:'num', minWidth:120}">{{trans('business.table.header.goods_num')}}</th>
                <th lay-data="{field:'view_num', minWidth:100}">{{trans('business.table.header.view_num')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'score', minWidth:120}">{{trans('business.table.header.shop_score')}}</th>
                <th lay-data="{field:'user_avatar', minWidth:80}">{{trans('user.table.header.user_avatar')}}</th>
                <th lay-data="{field:'user_bg', minWidth:80}">{{trans('user.table.header.user_cover')}}</th>
                <th lay-data="{field:'quality', minWidth:120}">{{trans('business.table.header.quality')}}</th>
                <th lay-data="{field:'service', minWidth:120}">{{trans('business.table.header.service')}}</th>
                <th lay-data="{field:'recommended_at', minWidth:160}">{{trans('business.table.header.recommended_at')}}</th>
                <th lay-data="{field:'user_phone', minWidth:150}">{{trans('user.form.label.phone')}}</th>
                <th lay-data="{field:'user_address', minWidth:200}">{{trans('business.table.header.address')}}</th>

                <th lay-data="{field:'admin_username', maxWidth:180, minWidth:150, @if(auth()->user()->admin_id!=1) hide:'true' @endif templet: function(field){
                    return field.admin_username; },event:'updateAmin'}">{{trans('business.table.header.manager')}}</th>

                <th lay-data="{field:'user_about', minWidth:200}">{{trans('user.table.header.user_about')}}</th>
                <th lay-data="{field:'user_verified_at', minWidth:160}">{{trans('user.table.header.user_audit_time')}}</th>
                <th lay-data="{fixed: 'right', width:200, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->user_id}}</td>
                    <td>{{$value->country}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_nick_name}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->user_verified==-1) layui-btn-danger @elseif($value->user_verified==0) layui-btn-warm @else layui-btn-normal @endif">
                            @if($value->user_verified==1) YES @elseif($value->user_verified==0) YES @else Unreviewed @endif
                        </span>
                    </td>
                    <td>{{$value->user_verified}}</td>
                    <td><input type="checkbox" @if($value->user_delivery>0) checked @endif name="user_delivery" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><input type="checkbox" @if($value->recommend>0) checked @endif name="recommend" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>@if(!empty($value->num)){{$value->num}}@else 0 @endif</td>
                    <td>@if(!empty($value->view_num)){{$value->view_num}}@else 0 @endif</td>
                    <td>{{$value->user_created_at}}</td>
                    <td>@if(!empty($value->score)){{$value->score}}@else 0 @endif</td>
                    <td><img src="@if(stripos($value->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$value->user_avatar}}@else{{$value->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                    <td>@if(!empty($value->user_bg))<img src="@if(stripos($value->user_bg, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$value->user_bg}}@else{{$value->user_bg}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" />@endif</td>

                    <td>@if(!empty($value->quality)){{$value->quality}}@else 0 @endif</td>
                    <td>@if(!empty($value->service)){{$value->service}}@else 0 @endif</td>

                    <td>@if($value->recommended_at!='0000-00-00 00:00:00'){{$value->recommended_at}}@endif</td>
                    <td>{{$value->user_phone}}</td>
                    <td>{{$value->user_address}}</td>
                    <td>{{$value->admin_username}}</td>
                    <td>{{$value->user_about}}</td>
                    <td>@if($value->user_verified_at!='0000-00-00 00:00:00'){{$value->user_verified_at}}@endif</td>
                    <td>{{$value->user_created_at}}</td>
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
            timePicker: 'lay/modules/admin/timePicker'
        }).use(['common' , 'table' , 'layer' , 'timePicker' , 'dropdown'], function () {
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                dropdown = layui.dropdown,
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
                if(layEvent === 'view'){
                    open(['95%','95%'], '/backstage/business/shop/view/'+data.user_id)
                }
                if(layEvent === 'follow'){
                    open(['95%','95%'], '/backstage/business/shop/follow/'+data.user_id)
                }else if(obj.event ==='updateAmin') {
                    dropdown.render({
                        elem: this
                        ,show: true
                        ,data: @json($admins)
                        ,click: function(obj){
                            let params = {'admin_id':obj.id};
                            common.confirm("{{trans('common.confirm.update')}}" , function(){
                                common.ajax("{{url('/backstage/business/shop')}}/"+data.user_id, params, function(res){
                                    location.reload();
                                }, 'patch');
                            } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                                table.render();
                            });
                        }
                    });
                }
            });
            form.on('radio(radio)', function(data){
                let value  = data.value;
                let t = data.type;
                let elem   = data.elem;
                let that = this;
                let thatVal = $(that).val();
                let id = data.othis.parent().attr('id');
                let n = $(elem).attr('name');
                @if(!Auth::user()->can('business::shop.update'))
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
                    common.ajax("{{url('/backstage/business/shop')}}/"+id, params , function(res){
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
                console.log(data);
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
            function request(data, params, name, checked=false) {
                @if(!Auth::user()->can('business::shop.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                @endif;
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/shop')}}/"+data.id, JSON.parse(params) , function(res){
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


            function open(area, content, types=2) {
                layer.open({
                    type: types,
                    shadeClose: true,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    content: content,
                });
            }
            $(function () {
                let img_show = null; // tips提示
                $('td img').hover(function(){
                    let img = "<img class='img_msg' src='"+$(this).attr('src')+"' style='max-height:300px;min-height: 100px;' />";
                    img_show = layer.tips(img, this, {tips:1});
                },function(){});
            });
            form.render();
        });

    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="view">{{trans('business.table.header.view_history')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="follow">{{trans('user.table.button.follow')}}</a>
    </script>
@endsection