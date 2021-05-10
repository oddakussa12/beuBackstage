@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">UserID:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Country</label>
                    <div class="layui-input-inline">
                        <select name="country" lay-verify="required" xm-select="country" lay-filter="country">
                            <option value="670"  @if(!empty($country)&&$country==670) selected @endif>East Timor</option>
                            <option value="62"  @if(!empty($country)&&$country==62) selected @endif>Indonesia</option>
                            <option value="1473  @if(!empty($country)&&$country==1473) selected @endif">Grenada</option>
                            <option value="251"  @if(!empty($country)&&$country==251) selected @endif>Ethiopia</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="dateTime" name="dateTime" placeholder="yyyy-MM-dd" value="@if(!empty($dateTime)){{$dateTime}}@endif">
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
                <th  lay-data="{field:'user_id', minWidth:120 ,fixed: 'left'}">ID</th>
                <th  lay-data="{field:'date', minWidth:120 ,fixed: 'left'}">Date</th>
                <th  lay-data="{field:'user_name', minWidth:190}">userName</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">userNickName</th>
                <th  lay-data="{field:'phone', minWidth:200}">Phone</th>
                <th  lay-data="{field:'friend', minWidth:100}">Friend</th>
                <th  lay-data="{field:'new', minWidth:100}">New</th>
                <th  lay-data="{field:'detail', width:160}">Detail</th>
                <th  lay-data="{field:'user_created_at', width:170}">UserCreatedAt</th>
                <th  lay-data="{field:'created_at', width:160}">CreatedAt</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td>{{$user->date}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->phone_country.$user->phone}}</td>
                    <td>{{$user->friend}}</td>
                    <td>{{$user->new}}</td>
                    <td>{{$user->detail}}</td>
                    <td>{{$user->user_created_at}}</td>
                    <td>{{ $user->created_at }}</td>
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
