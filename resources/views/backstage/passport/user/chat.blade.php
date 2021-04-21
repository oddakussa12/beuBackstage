@extends('layouts.app')
@section('title', trans('common.header.title'))
<div  class="layui-fluid">
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                <div class="layui-input-inline">
                    <input type="hidden" id="user_id" name="user_id" value="{{$user_id}}">
                    <input type="text" class="layui-input" id="dateTime" name="dateTime" placeholder="yyyy-MM-dd" value="@if(!empty($dateTime)){{$dateTime}}@else{{date('Y-m-d', time())}}@endif">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                <div class="layui-input-inline">
                    <select  name="sort">
                        <option value="chat_from_id" @if(!empty($sort)&&$sort=='chat_from_id') selected @endif>Quantity sent</option>
                        <option value="chat_to_id" @if(!empty($sort)&&$sort=='chat_to_id') selected @endif>Quantity received</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </div>
    </form>
    <table class="layui-table">
        <thead>
        <tr>
            <th  lay-data="{field:'receive', minWidth:120,sort:true}">{{trans('user.table.header.message_received')}}</th>
            <th  lay-data="{field:'send', minWidth:120,sort:true}">{{trans('user.table.header.message_send')}}</th>
            <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
            <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>
            <th  lay-data="{field:'time', width:160,sort:true}">{{trans('user.table.header.last_active_time')}}</th>
            <th  lay-data="{field:'ip', width:130}">{{trans('user.table.header.last_active_ip')}}</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($users))
        @foreach($users as $user)
            <tr>
                <td>{{$user->receive}}</td>
                <td>{{$user->send}}</td>
                <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
                <td>{{ $user->country }}</td>
                <td>{{$user->time}}</td>
                <td>{{$user->ip}}</td>
            </tr>
        @endforeach
        @else
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endif
        </tbody>
    </table>
    @if(empty($appends))
        {{ $users->links('vendor.pagination.default') }}
    @else
        {{ $users->appends($appends)->links('vendor.pagination.default') }}
    @endif


</div>
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            let table = layui.table,
                flow = layui.flow,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
            }); 
        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
