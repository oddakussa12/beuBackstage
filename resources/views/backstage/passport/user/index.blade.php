@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <select name="field">
                            <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                            @foreach(trans('user.form.select.query') as $key=>$translation)
                            <option value="{{$key}}" @if(!empty($field)&&$field==$key) selected @endif>{{$translation}}</option>
                            @endforeach
                        </select>
                    </div>
                <div class="layui-form-mid layui-word-aux">:</div>

                <div class="layui-input-inline">
                    <input class="layui-input" name="value" id="value"  @if(!empty($value)) value="{{$value}}" @endif />
                </div>
            </div>

            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_country_id')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="country_code" lay-verify="" lay-search  style="width: 500px;">
                            <option value="">{{trans('user.form.placeholder.user_country_id')}}</option>
                            @foreach($counties  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <a href="{{route('passport::user.export')}}@if(!empty($query))?{{$query}}@endif" class="layui-btn" target="_blank">{{trans('common.form.button.export')}}</a>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="user_table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', minWidth:100 ,fixed: 'left'}">用户{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150 ,fixed: 'left'}">用户昵称</th>
                <th  lay-data="{field:'user_name', minWidth:190}">用户名</th>
                <th  lay-data="{field:'user_email', minWidth:200}">{{trans('user.table.header.user_email')}}</th>
                <th  lay-data="{field:'user_level', width:100}">{{trans('user.table.header.user_level')}}</th>
                <th  lay-data="{field:'user_is_block', width:100}">{{trans('user.table.header.user_block')}}</th>
                <th  lay-data="{field:'user_deleted', width:100}">{{trans('user.table.header.user_deleted')}}</th>
                <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
                <th  lay-data="{field:'user_last_name', width:150}">{{trans('user.table.header.user_last_name')}}</th>
                <th  lay-data="{field:'user_country', width:150}">{{trans('user.table.header.user_country_id')}}</th>
{{--                <th  lay-data="{field:'user_google', width:80}">{{trans('user.table.header.user_google')}}</th>--}}
                <th  lay-data="{field:'user_ip_address', width:150}">{{trans('user.table.header.user_ip_address')}}</th>
                <th  lay-data="{field:'user_op', minWidth:250 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$user->user_id}}">{{$user->user_id}}</a></td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_email}}</td>
                    <td><input type="checkbox" @if($user->user_level>0) checked @endif name="user_level" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><input type="checkbox" @if($user->is_block==true) checked @endif name="is_block" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td><input type="checkbox" @if(!empty($user->user_deleted_at)) checked @endif name="user_deleted" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{ $user->user_format_created_at }}</td>
                    <td>{{ $user->user_last_name }}</td>
                    <td>{{ $user->user_country }}</td>
{{--                    <td>{{ $user->user_google }}</td>--}}
                    <td>{{ $user->user_ip_address }}</td>

                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $users->links('vendor.pagination.default') }}
        @else
            {{ $users->appends($appends)->links('vendor.pagination.default') }}
        @endif


    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="password">{{trans('user.table.button.password')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="fan">{{trans('user.table.button.fan')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer,
                flow = layui.flow,
                timePicker = layui.timePicker,
                laydate = layui.laydate;




            table.init('user_table', { //转化静态表格
                page:false
            });

            // var nowDate = laydate.render({
            //     elem: '#dateTime',
            //     type: 'datetime',
            //     range: true
            // });

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

            table.on('tool(user_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据

                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'block'||layEvent === 'unblock'){ //删除
                    layer.confirm("{{trans('common.confirm.operate')}}", function(index){
                        common.ajax("{{url('/backstage/passport/user')}}/"+data.user_id+'/'+layEvent , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'put');


                        //向服务端发送删除指令
                    });
                } else if(layEvent === 'edit'){ //编辑
                    location.href='/{{app()->getLocale()}}/backstage/passport/user/'+data.user_id+'/edit';
                }else if(layEvent === 'fan'){

                    var user_id = data.user_id;
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['80%','80%'],
                        offset: 'auto',
                        content: '/{{app()->getLocale()}}/backstage/passport/user/'+user_id+'/fan',
                    });
                }else if(layEvent === 'password'){
                    var user_id = data.user_id;
                    layer.prompt({
                        title: '请填写密码',
                        value: '123456',
                        yes: function(index, layer_prompt){
                            var value = layer_prompt.find(".layui-layer-input").val();
                            alert("输入值为：" + value);
                        }
                    });
                }
            });
            form.on('submit(btnSubmit)', function (data) {

            });
            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var userId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                var name = $(data.elem).attr('name');
                if(name=='is_block')
                {
                    @if(!Auth::user()->can('passport::user.block'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                    @endif
                }else{
                    @if(!Auth::user()->can('passport::user.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                    @endif
                }
                if(checked)
                {
                    var params = '{"'+name+'":"on"}';
                    var event = 'block';
                }else{
                    var event = 'unblock';
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                if(name=='is_block')
                {
                    var url = "{{url('/backstage/passport/user')}}/"+userId+'/'+event;
                }else{
                    var url = "{{url('/backstage/passport/user')}}/"+userId;
                }
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax(url , JSON.parse(params) , function(res){
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
            flow.lazyimg();

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
