@extends('layouts.app')
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'money', minWidth:180, edit:'text'}">Deposits</th>
                <th lay-data="{field:'balance', minWidth:180}">Balance</th>
                <th lay-data="{field:'money_time', minWidth:180}">DepositsTime</th>
                <th lay-data="{field:'admin_username', minWidth:180}">Operator</th>
                <th lay-data="{field:'created_at', minWidth:180}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $item)
                <tr>
                    <td>{{$item->money}}</td>
                    <td>{{$item->balance}}</td>
                    <td>{{$item->money_time}}</td>
                    <td>{{$item->admin_username}}</td>
                    <td>{{$item->created_at}}</td>
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
            base: "{{url('plugin/layui')}}/",
        }).use(['table'], function () {
            const table = layui.table;
                table.init('table', {
                page:false
            });
        });
    </script>
@endsection