@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.keyword')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                    <div class="layui-input-inline">
                        <select name="country_code" lay-verify="" xm-select="country" lay-filter="country">
                            <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                            @foreach($countries  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="dateTime" readonly name="dateTime" placeholder="yyyy-MM-dd" value="@if(!empty($dateTime)){{$dateTime}}@endif">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', minWidth:120 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'date', minWidth:120 ,fixed: 'left'}">{{trans('operation.table.header.active_user.date')}}</th>
                <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
                <th  lay-data="{field:'phone', minWidth:200}">{{trans('user.table.header.phone')}}</th>
                <th  lay-data="{field:'friend', minWidth:100}">{{trans('operation.table.header.active_user.friend')}}</th>
                <th  lay-data="{field:'new', minWidth:100}">{{trans('operation.table.header.active_user.new')}}</th>
                <th  lay-data="{field:'detail', width:160}">{{trans('operation.table.header.active_user.detail')}}</th>
                <th  lay-data="{field:'user_created_at', width:170}">{{trans('user.table.header.user_registered')}}</th>
                <th  lay-data="{field:'created_at', width:160}">{{trans('common.table.header.created_at')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td>{{$user->date}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->user_phone_country.$user->user_phone}}</td>
                    <td>{{$user->friend}}</td>
                    <td>{{$user->new}}</td>
                    <td>{{$user->detail}}</td>
                    <td>{{$user->user_created_at}}</td>
                    <td>{{$user->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $users->links('vendor.pagination.default') }}
        @else
            {{ $users->appends($appends)->links('vendor.pagination.default') }}
        @endif


    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="password">{{trans('user.table.button.password')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="fan">{{trans('user.table.button.fan')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
            });
            table.init('table', { //转化静态表格
                page:false
            });
        })
    </script>
@endsection
