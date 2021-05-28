@extends('layouts.app')
    <style>
        table td { height: 40px; line-height: 40px;}
        table td img { max-height: 30px; min-width: 40px; }
        .layer-alert-video .layui-layer-content img {width: 100%;}
    </style>

    <div  class="layui-fluid">
        <br>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.status')}}:</label>
                    <div class="layui-input-inline">
                        <select name="status">
                            <option value="">ALL</option>
                            <option value="pass" @if(isset($status) && $status=='pass') selected @endif>Pass</option>
                            <option value="refuse" @if(isset($status) && $status=='refuse') selected @endif>Refuse</option>
                            <option value="recommend" @if(isset($status) && $status=='recommend') selected @endif>Recommend</option>
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
                <th lay-data="{field:'audit_id', minWidth:100}">CommentID</th>
                <th lay-data="{field:'type', minWidth:100}">Type</th>
                <th lay-data="{field:'status', minWidth:110}">Status</th>
                <th lay-data="{field:'goods_id', minWidth:100}">GoodsId</th>
                <th lay-data="{field:'content', minWidth:300}">Content</th>
                <th lay-data="{field:'comment_at', minWidth:160}">CommentAt</th>
                <th lay-data="{field:'created_at', minWidth:160}">AuditAt</th>
                <th lay-data="{fixed: 'right', width:120, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $value)
                <tr>
                    <td>{{$value->audit_id}}</td>
                    <td>{{$value->type}}</td>
                    <td>{{$value->status}}</td>
                    <td>{{$value->comment->goods_id}}</td>
                    <td>{{$value->comment->content}}</td>
                    <td>{{$value->comment->created_at}}</td>
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
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['table' , 'layer', 'timePicker'], function () {
            const layer = layui.layer,
                timePicker = layui.timePicker,
                table = layui.table;
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
                layer.open({
                    type: 2,
                    shadeClose: true,
                    shade: 0.8,
                    area: ['95%','95%'],
                    offset: 'auto',
                    scrollbar:true,
                    content: '/backstage/business/shop/manager/detail/'+data.admin_id,
                });
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="detail">{{trans('common.table.button.detail')}}</a>
    </script>
@endsection