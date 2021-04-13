@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <table class="layui-table"  lay-filter="user_table">
        <thead>
        <tr>
            <th  lay-data="{field:'ip', minWidth:130 ,fixed: 'left'}">Type</th>
            <th  lay-data="{field:'time', minWidth:80}">Score</th>
            <th  lay-data="{field:'status', minWidth:150}">{{trans('common.table.header.created_at')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->type}}</td>
                <td>{{$l->score}}</td>
                <td>{{$l->created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $list->links('vendor.pagination.default') }}
    @else
        {{ $list->appends($appends)->links('vendor.pagination.default') }}
    @endif


</div>
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
        }).use(['common' , 'table', 'element'], function () {
            let table = layui.table;
            table.init('user_table', { //转化静态表格
                page:false
            });
        })
    </script>
@endsection
