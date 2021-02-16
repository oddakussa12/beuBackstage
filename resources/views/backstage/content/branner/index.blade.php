@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="branner_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:360}">ID</th>
                <th  lay-data="{field:'type', width:360}">类型</th>
                <th  lay-data="{field:'value', width:360}">值</th>
                <th  lay-data="{field:'image', width:360}">图片</th>
                <th  lay-data="{field:'sort', width:360}">排序</th>
                <th  lay-data="{field:'status', width:360}">状态</th>
                <th  lay-data="{field:'created_at', width:360}">日期th>
            </tr>
            </thead>
            <tbody>
            @foreach($banners as $key=>$banner)
                <tr>
                    <td>{{ $banner->id }}</td>
                    <td>{{ $banner->type }}</td>
                    <td>{{ $banner->value }}</td>
                    <td>{{ $banner->image }}</td>
                    <td>{{ $banner->sort }}</td>
                    <td>{{ $banner->status }}</td>
                    <td>{{ $banner->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(empty($appends))
            {{ $banners->links('vendor.pagination.default') }}
        @else
            {{ $banners->appends($appends)->links('vendor.pagination.default') }}
        @endif

@endsection


