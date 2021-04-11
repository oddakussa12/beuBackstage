@extends('layouts.dashboard')
@section('layui-content')
    <form class="layui-form">
        <div class="layui-inline">
            <button id="add" type="button" class="layui-btn layui-btn-normal">Add</button>
        </div>
    </form>
    <table class="layui-table"   lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', minWidth:110 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_nickname', minWidth:190}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'rank', minWidth:120}">Rank</th>
            <th  lay-data="{field:'score', minWidth:120, edit:'text'}">Score</th>
            <th  lay-data="{field:'user_time', minWidth:150}">{{trans('user.table.header.user_time')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $l)
            <tr>
                <td>{{$l->user_id}}</td>
                <td>{{$l->user_name}}</td>
                <td>{{$l->user_nick_name}}</td>
                <td>{{$l->rank}}</td>
                <td>{{$l->score}}</td>
                <td>{{$l->user_created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $users->links('vendor.pagination.default') }}
    @else
        {{ $users->appends($appends)->links('vendor.pagination.default') }}
    @endif
    <div class="layui-tab-item"  id="layui-echarts">
        <div id="container" style="height: 100%">
        </div>
    </div>
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table', 'common', 'laydate'], function () {
            let $ = layui.jquery,
                common = layui.common,
                table = layui.table;

            table.init('table', {
                page:false
            });
            //监听单元格编辑
            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field //得到字段
                    ,original = $(this).prev().text(); //得到字段
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::props.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/operator/virtual')}}/"+data.user_id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                        window.location.reload();
                    } , 'PATCH' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            obj.update(d);
                            $(that).val(original);
                            table.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                    d[field] = original;
                    obj.update(d);
                    $(that).val(original);
                    table.render();
                });
            });
            $(document).on('click','#add',function(){
                layer.open({
                    type: 2,
                    shadeClose: true,
                    shade: 0.8,
                    area: ['40%','40%'],
                    offset: 'auto',
                    content: '/backstage/operator/virtual/create',
                });
            });
        })
    </script>
@endsection
