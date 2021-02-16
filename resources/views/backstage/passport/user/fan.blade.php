@extends('layouts.app')
@section('content')
    <div  class="layui-row">
        <div class="layui-col-md6">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>{{trans('fan.html.fan_list')}}</legend>
            </fieldset>
            <table class="layui-table"   lay-filter="fan_table" id="fan_table" >

                <thead>
                <tr>
                    <th lay-data="{field:'id', width:100 ,fixed: 'left'}">{{trans('fan.table.header.id')}}</th>
                    <th lay-data="{field:'user_id', width:200,fixed: 'left'}">{{trans('fan.table.header.user_id')}}</th>
                    <th lay-data="{field:'user_name', width:200 ,fixed: 'left'}">{{trans('fan.table.header.user_name')}}</th>
                    <th lay-data="{field:'created_at', width:200}">{{trans('fan.table.header.fan_create')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fans as $fan)
                    <tr>
                        <td>{{$fan->id}}</td>
                        <td>{{$fan->user_id}}</td>
                        <td>{{$fan->user_name}}</td>
                        <td>{{$fan->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $fans->links('vendor.pagination.default') }}
        </div>
        <div class="layui-col-md6">
            <form class="layui-form"   lay-filter="fan_form">
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>用户名</legend>
                </fieldset>


                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入内容" class="layui-textarea" name="fan" contentEditable="true"></textarea>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-sm" lay-submit lay-filter="fan_submit_btn">提交</button>
                    </div>
                </div>
            </form>
        </div>
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
        }).use(['common' , 'table' , 'layer' , 'carousel'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery;
            form.on('submit(fan_form)', function(data){
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });
            form.on('submit(fan_submit_btn)', function(data){
                @if(!Auth::user()->can('passport::user.follow'))
                data.form.reset();
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.elem);
                form.render();
                return false;
                @endif
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/passport/user/'.$id.'/fan')}}" , data.field , function(res){
                        location.reload();
                    } , 'post');
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });





            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var commentId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('content::comment.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                var name = $(data.elem).attr('name');
                if(checked)
                {
                    var params = '{"'+name+'":"on"}';
                }else{
                    var params = '{"'+name+'":"off"}';
                }
                form.render();

            });




            table.init('fan_table', { //转化静态表格
                page:false,
                height: 400 //固定值
            });

        });
    </script>


@endsection