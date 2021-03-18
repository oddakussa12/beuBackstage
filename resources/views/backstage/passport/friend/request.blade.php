@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">From:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="fuzzy search" name="from" id="from"  @if(!empty($from)) value="{{$from}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">To:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="fuzzy search" name="to" id="to"  @if(!empty($to)) value="{{$to}}" @endif />
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="common_table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', width:130 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_name', width:130 ,fixed: 'left'}">{{trans('user.table.header.user_name')}}</th>
                <th  lay-data="{field:'user_nick_name', width:130 ,fixed: 'left'}">{{trans('user.table.header.user_nick_name')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{$request->request_from_id}}</td>
                    <td>{{$request->request_from_id}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $requests->links('vendor.pagination.default') }}
        @else
            {{ $requests->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            echarts: 'lay/modules/echarts',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'element' , 'element', 'flow' , 'laydate' , 'timePicker', 'echarts'], function () {
            let $ = layui.jquery,
                table = layui.table,
                flow = layui.flow,
                echarts = layui.echarts,
                element = layui.element,
                timePicker = layui.timePicker;
            table.init('common_table', { //转化静态表格
                page:false
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
