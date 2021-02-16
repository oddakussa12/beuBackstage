@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
{{--        <fieldset class="layui-elem-field layui-field-title">--}}
{{--            <legend>热门话题</legend>--}}
{{--        </fieldset>--}}
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">订单状态</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">全部</option>
                        <option value="0" @if(isset($status) && $status=='0') selected @endif>未兑换</option>
                        <option value="1" @if(isset($status) && $status=='1') selected @endif>已兑换</option>
                    </select>
                </div>
                <label class="layui-form-label">用户ID:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_id" id="user_id" @if(!empty($user_id)) value="{{$user_id}}" @endif/>
                </div>
                <label class="layui-form-label">用户名:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_name" id="user_name" @if(!empty($user_name)) value="{{$user_name}}" @endif/>
                </div>
                <label class="layui-form-label">手机号:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="phone" id="phone" @if(!empty($phone)) value="{{$phone}}" @endif/>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">商品ID:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="good_id" id="good_id" @if(!empty($good_id)) value="{{$good_id}}" @endif/>
                    </div>
                    <label class="layui-form-label">兑换时间:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$data->total()}}</div>
                </div>

            </div>
        </form>
        <table class="layui-table"   lay-filter="post_table" id="post_table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:100, sort:true}">订单号</th>
                <th lay-data="{field:'good_image', width:100}">商品图片</th>
                <th lay-data="{field:'good_id', width:80}">商品ID</th>
                <th lay-data="{field:'user_id', width:100}">用户ID</th>
                <th lay-data="{field:'price', width:100, sort:true}">实际价格</th>
                <th lay-data="{field:'score', width:100, sort:true}">消耗积分</th>
                <th lay-data="{field:'good_name', width:120}">商品名称</th>
                <th lay-data="{field:'user_name', width:140}">用户昵称</th>
                <th lay-data="{field:'user_real_name', width:160}">用户姓名</th>
                <th lay-data="{field:'country', width:80}">国家</th>
                <th lay-data="{field:'phone', width:150}">手机号</th>
                <th lay-data="{field:'status', width:110}">状态</th>
                <th lay-data="{field:'created_at', width:160, sort:true}">创建时间</th>
                <th lay-data="{field:'updated_at', width:160}">修改时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td><img src="{{config('common.qnUploadDomain.thumbnail_domain')}}/{{$value->good_image}}?imageView2/0/w/30/h/30/interlace/1|imageslim"></td>
                    <td>{{$value->good_id}}</td>
                    <td>{{$value->user_id}}</td>
                    <td>{{$value->price}}</td>
                    <td>{{$value->score}}</td>
                    <td>{{$value->good_name}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->user_real_name}}</td>
                    <td>{{$value->country}}</td>
                    <td>{{$value->phone}}</td>
                    <td><input type="checkbox" @if($value->status==1) checked @endif name="status" lay-skin="switch" lay-filter="switchAll" lay-text="已兑换|未兑换"></td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $data->links('vendor.pagination.default') }}
        @else
            {{ $data->appends($appends)->links('vendor.pagination.default') }}
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
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker;
            table.on('tool(post_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
               if(layEvent === 'edit'){ //编辑
                    console.log(data);
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: '邀请好友活动设置',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['60%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/invitation/activity/'+id+'/edit',
                    });
                }
            });
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var postId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('invitation::activity.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                var name = $(data.elem).attr('name');
                if(checked) {
                    var params = '{"'+name+'":"on"}';
                }else {
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/invitation/order')}}/"+postId , JSON.parse(params) , function(res){
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

            table.init('post_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });
        });
    </script>
@endsection
<script>

</script>