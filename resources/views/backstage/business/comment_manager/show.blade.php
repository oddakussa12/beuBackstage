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
                    <label class="layui-form-label">{{trans('business.form.label.comment_manager.status')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="status">
                            @foreach(trans('business.form.select.comment_manager.status') as $k=>$v)
                                <option value="{{$k}}" @if(isset($status) && $status==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'audit_id', minWidth:150}">{{trans('business.table.header.goods_comment.id')}}</th>
                <th lay-data="{field:'type', minWidth:100}">{{trans('business.table.header.comment_manager.type')}}</th>
                <th lay-data="{field:'status', minWidth:80}">{{trans('business.table.header.comment_manager.status')}}</th>
                <th lay-data="{field:'goods_id', minWidth:150}">{{trans('business.table.header.goods.id')}}</th>
                <th lay-data="{field:'content', minWidth:100}">{{trans('business.table.header.goods_comment.content')}}</th>
                <th lay-data="{field:'created_at', minWidth:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($audits as $value)
                <tr>
                    <td>{{$value->audit_id}}</td>
                    <td>{{$value->type}}</td>
                    <td>{{$value->status}}</td>
                    <td>{{$value->comment->goods_id??''}}</td>
                    <td>{{$value->comment->content??''}}</td>
                    <td>{{$value->created_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $audits->links('vendor.pagination.default') }}
        @else
            {{ $audits->appends($appends)->links('vendor.pagination.default') }}
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
        }).use(['table' , 'timePicker'], function () {
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
                    locale:"{{locale()}}"
                },
            });
        });
    </script>
@endsection