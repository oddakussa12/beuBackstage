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
                        <input class="layui-input" name="keyword" placeholder="{{trans('business.table.header.shop_name')}}" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.phone')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="phone" placeholder="{{trans('user.form.label.phone')}}" id="phone" @if(!empty($phone)) value="{{$phone}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.status')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="state">
                            <option value="">ALL</option>
                            <option value="1" @if(isset($state) && $state=='1') selected @endif>Normal</option>
                            <option value="-1" @if(isset($state) && $state=='-1') selected @endif>UnReviewed</option>
                            <option value="0" @if(isset($state) && $state=='0') selected @endif>Refuse</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="">CreatedAt</option>
                            <option value="num" @if(isset($sort) && $sort=='num') selected @endif>GoodsNum</option>
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
                <th lay-data="{field:'user_id', minWidth:130}">{{trans('business.table.header.shop_id')}}</th>
                <th lay-data="{field:'user_avatar', minWidth:80}">{{trans('user.table.header.user_avatar')}}</th>
                <th lay-data="{field:'user_cover', minWidth:80}">{{trans('user.table.header.user_cover')}}</th>
                <th lay-data="{field:'user_name', minWidth:160}">{{trans('business.table.header.shop_name')}}</th>
                <th lay-data="{field:'user_nick_name', minWidth:160}">{{trans('business.table.header.shop_nick_name')}}</th>
                <th lay-data="{field:'user_verified', minWidth:230}">{{trans('common.table.header.status')}}</th>
                <th lay-data="{field:'user_level', minWidth:100}">{{trans('business.table.header.vip')}}</th>
                <th lay-data="{field:'num', minWidth:120}">{{trans('business.table.header.goods_num')}}</th>
                <th lay-data="{field:'view_num', minWidth:120}">{{trans('business.table.header.view_num')}}</th>
                <th lay-data="{field:'recommend', minWidth:120}">{{trans('business.table.header.recommend')}}</th>
                <th lay-data="{field:'recommended_at', minWidth:160}">{{trans('business.table.header.recommended_at')}}</th>
                <th lay-data="{field:'country', minWidth:100}">{{trans('user.form.label.user_country')}}</th>
                <th lay-data="{field:'user_phone', minWidth:150}">{{trans('user.form.label.phone')}}</th>
                <th lay-data="{field:'user_address', minWidth:200}">{{trans('business.table.header.address')}}</th>
                <th lay-data="{field:'user_about', minWidth:200}">{{trans('user.table.header.user_about')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->user_id}}</td>
                    <td><img src="@if(stripos($value->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$value->user_avatar}}@else{{$value->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                    <td>@if(!empty($value->user_cover))<img src="@if(stripos($value->user_cover, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$value->user_cover}}@else{{$value->user_cover}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" />@endif</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_nick_name}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($value->user_verified==-1) layui-btn-danger @elseif($value->user_verified==0) layui-btn-warm @else layui-btn-normal @endif">
                            @if($value->user_verified==1) Normal @elseif($value->user_verified==0) Refuse @else UnAudited @endif
                        </span>
                        @if($value->user_verified!=1)
                            <input type="radio" name="audit" lay-filter="radio" value="pass" title="Pass">
                            @if($value->user_verified!=0)<input type="radio" name="audit" lay-filter="radio" value="refuse" title="Refuse">@endif
                        @endif
                    </td>
                    <td><input type="checkbox" @if($value->user_level==1) checked @endif name="level" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>@if(!empty($value->num)){{$value->num}}@else 0 @endif</td>
                    <td>@if(!empty($value->view_num)){{$value->view_num}}@else 0 @endif</td>
                    <td><input type="checkbox" @if($value->recommend>0) checked @endif name="recommend" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>@if($value->recommended_at!='0000-00-00 00:00:00'){{$value->recommended_at}}@endif</td>
                    <td>{{$value->country}}</td>
                    <td>{{$value->user_phone}}</td>
                    <td>{{$value->user_address}}</td>
                    <td>{{$value->user_about}}</td>
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
                let level  = data.value;
                const name = $(data.elem).attr('name');
                let params = '{"' + name + '":"'+level+'"}';
                data.id    = data.othis.parents('tr').find("td :first").text();
                request(data, params);
                location.reload();
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
                request(data, params, checked);
            });
            function request(data, params, checked=false) {
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
                        content: '/backstage/business/shop/view/'+data.user_id,
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