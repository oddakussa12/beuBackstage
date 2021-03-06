@extends('layouts.dashboard')
@section('layui-content')
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="" name="keyword" id="keyword"  @if(!empty($appends['keyword'])) value="{{$appends['keyword']}}" @endif />
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </div>
    </form>
    <table class="layui-table"  lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
            <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
            <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
            <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>
            <th  lay-data="{field:'time', width:160,sort:true}">{{trans('user.table.header.last_active_time')}}</th>
            <th  lay-data="{field:'activation', width:70}">{{trans('user.table.header.user_activation')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->user_id}}</td>
                <td><img width="32px;" src="{{splitJointQnImageUrl($user->user_avatar)}}" /></td>
                <td>{{$user->user_nick_name}}</td>
                <td>{{$user->user_name}}</td>
                <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
                <td>{{ $user->user_country }}</td>
                <td>{{$user->activeTime}}</td>
                <td><span class="layui-btn layui-btn-xs">@if($user->user_activation==1) YES @else NO @endif</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(empty($appends))
        {{ $users->links('vendor.pagination.default') }}
    @else
        {{ $users->appends($appends)->links('vendor.pagination.default') }}
    @endif
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-btn-container">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="friend">{{trans('user.table.button.friend')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="history">{{trans('user.table.button.history')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
            echarts: 'lay/modules/echarts',
        }).use(['element' , 'table' , 'timePicker'], function () {
            let $ = layui.jquery,
                table = layui.table,
                timePicker = layui.timePicker;

            timePicker.render({
                elem: '#dateTime', //???????????????input??????
                options:{      //????????????timeStamp???format
                    timeStamp:false,//true??????????????? ?????????format?????????????????????false??????????????? //??????false
                    format:'YYYY-MM-DD HH:ss:mm',//?????????????????????????????????moment.js?????? ?????????YYYY-MM-DD HH:ss:mm
                    locale:"{{locale()}}"
                },
            });
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
