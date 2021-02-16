@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <table class="layui-table"   lay-filter="feedback_table" id="feedback_table" >

            <thead>
            <tr>
                <th lay-data="{field:'feedback_id', width:80}">{{trans('feedback.table.header.feedback_id')}}</th>
                <th lay-data="{field:'feedback_name', width:200}">{{trans('feedback.table.header.feedback_name')}}</th>
                <th lay-data="{field:'feedback_email', width:200}">{{trans('feedback.table.header.feedback_email')}}</th>
                <th lay-data="{field:'feedback_content', width:800}">{{trans('feedback.table.header.feedback_content')}}</th>
                <th lay-data="{field:'feedback_created_at', width:200}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'feedback_result', minWidth:100}">{{trans('feedback.table.header.feedback_result')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($feedbacks as $feedback)
                <tr>
                    <td>{{$feedback->feedback_id}}</td>
                    <td>{{$feedback->feedback_name}}</td>
                    <td>{{$feedback->feedback_email}}</td>
                    <td>{{$feedback->feedback_content}}</td>
                    <td>{{$feedback->feedback_created_at}}</td>
                    <td>
                        <input type="checkbox" @if($feedback->feedback_result==1) checked @endif name="feedback_result" lay-skin="switch" lay-filter="switchAll" lay-text="done|wait">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $feedbacks->links('vendor.pagination.default') }}
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
        }).use(['common' , 'table' , 'layer'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                $=layui.jquery;
            $(document).on('click','#create',function(){
                layer.open({
                    type: 2,
                    title: '添加',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['80%','80%'],
                    offset: 'auto',
                    content: '/backstage/content/post/create',
                });
            });
            $(document).on('click','#clearCache',function(){
                layer.confirm("{{trans('common.confirm.clearCache')}}", function(index){
                    common.ajax("{{url('/backstage/content/clear/cache')}}" , {} , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.operate')}}" , 1 , 500 , 6 , 't' ,function () {
                            // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            layer.close(index);
                        });
                    } , 'get');
                });
            });

            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var feedbackId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('report::feedback.update'))
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
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/report/feedback/')}}/"+feedbackId , JSON.parse(params) , function(res){
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


            table.init('feedback_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });

        });
    </script>
    <script type="text/html" id="toolbar">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">{{trans('feedback.table.header.feedback_content')}}</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="feedback_content" id="feedback_content" value="{{$feedback_content}}" />
                </div>
                <button class="layui-btn" type="submit"  lay-submit >查询</button>
            </div>
        </form>
    </script>
@endsection
