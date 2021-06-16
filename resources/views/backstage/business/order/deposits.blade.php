@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'user_id', minWidth:180, hide:'true'}">UserId</th>
                <th lay-data="{field:'user_name', minWidth:180}">ShopName</th>
                <th lay-data="{field:'user_nick_name', minWidth:180}">ShopNickName</th>
                <th lay-data="{field:'money', minWidth:180, edit:'text'}">Deposits</th>
                <th lay-data="{field:'money_time', minWidth:180, edit:'text', event:'date'}">DepositsTime</th>
                <th lay-data="{field:'created_at', minWidth:180}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:180}">{{trans('common.table.header.updated_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shops as $shop)
                <tr>
                    <td>{{$shop->user_id}}</td>
                    <td>{{$shop->user_name}}</td>
                    <td>{{$shop->user_nick_name}}</td>
                    <td>{{$shop->money}}</td>
                    <td>{{$shop->money_time}}</td>
                    <td>{{$shop->created_at}}</td>
                    <td>{{$shop->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $shops->links('vendor.pagination.default') }}
        @else
            {{ $shops->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/",
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['table', 'laydate', 'common'], function () {
            const $ = layui.jquery,
                form = layui.form,
                table = layui.table,
                common = layui.common,
                laydate = layui.laydate;
                table.init('table', {
                page:false
            });
            //监听单元格编辑
            table.on('edit(table)', function (obj) {
                @if(!Auth::user()->can('business::discovery.deposits.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                let params= {};
                var value = obj.value //得到修改后的值
                    , data = obj.data //得到所在行所有键值
                    , field = obj.field; //得到字段
                let arg = /^\d+(\.\d+)?$/;
                if (!arg.exec(value)) {
                    layer.alert('You can only enter numbers and decimals', {icon: 5});
                    return false;
                }
                params[field] = value;
                params['user_id'] = data.user_id;
                request(params);
                // layer.msg('[ID: ' + data.user_id + '] ' + field + ' 字段更改为：' + value);
            });
            table.on('tool(table)', function (obj) {
                @if(!Auth::user()->can('business::discovery.deposits.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                var newdata = {};
                var params = d = {};
                var data = obj.data;
                if (obj.event === 'date') {
                    debugger
                    var field = $(this).data('field');
                    laydate.render({
                        elem: this.firstChild
                        , lang: 'en'
                        , show: true //直接显示
                        , closeStop: this
                        , type: 'datetime'
                        , format: "yyyy-MM-dd HH:mm:ss"
                        , done: function (value, date) {
                            newdata[field] = value;
                            obj.update(newdata);
                            params[field] = value;
                            params['user_id'] = data.user_id;
                            request(params);
                        }
                    });
                }
                if(obj.event=== 'detail'){
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%','95%'],
                        offset: 'auto',
                        content: '/backstage/business/discovery/money/'+data.user_id,
                    });
                }
                if(obj.event=== 'order'){
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%','95%'],
                        offset: 'auto',
                        content: '/backstage/business/discovery/order/detail/?user_id='+data.user_id,
                    });
                }
            });

            function request(params) {
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/business/discovery/deposits')}}", params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                         parent.location.reload();
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
            }
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="order">{{trans('business.table.header.order')}}</a>
    </script>
@endsection