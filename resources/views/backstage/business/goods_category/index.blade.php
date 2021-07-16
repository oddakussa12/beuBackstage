@extends('layouts.dashboard')
@section('layui-content')
    <style>
         table td { height: 40px; line-height: 40px;}
    </style>
    <div class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.form.label.goods_category.is_default')}}:</label>
                    <div class="layui-input-inline">
                        <select name="default">
                            <option value=""></option>
                            <option value="1" @if(isset($default) && $default=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($default) && $default=='0') selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
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
                <th lay-data="{field:'category_id', minWidth:180, hide:'true'}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'name', minWidth:180}">{{trans('common.form.label.name')}}</th>
                <th lay-data="{field:'goods_num', minWidth:130}">{{trans('business.table.header.goods_num')}}</th>
                <th lay-data="{field:'default', minWidth:110}">IsDefualt</th>
                <th lay-data="{field:'sort', minWidth:110}">{{trans('common.form.label.sort')}}</th>
                <th lay-data="{field:'created_at', minWidth:170}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'updated_at', minWidth:170}">{{trans('common.table.header.updated_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($goodsCategories as $goodsCategory)
                <tr>
                    <td>{{$goodsCategory->category_id}}</td>
                    <td>{{$goodsCategory->name}}</td>
                    <td>{{$goodsCategory->goods_num}}</td>
                    <td><span class="layui-btn layui-btn-xs @if(!empty($goodsCategory->default)) layui-btn-normal @else layui-btn-warm @endif">@if(!empty($goodsCategory->default)) YES @else NO @endif</span></td>
                    <td>{{$goodsCategory->sort}}</td>
                    <td>{{$goodsCategory->created_at}}</td>
                    <td>{{$goodsCategory->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $goodsCategories->links('vendor.pagination.default') }}
        @else
            {{ $goodsCategories->appends($appends)->links('vendor.pagination.default') }}
        @endif
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
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            let table = layui.table,
                timePicker = layui.timePicker;
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
        });
    </script>
@endsection