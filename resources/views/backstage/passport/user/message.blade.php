@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Name/ID:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="fuzzy search" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.phone')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" placeholder="fuzzy search" name="phone" id="phone"  @if(!empty($phone)) value="{{$phone}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="country_code" lay-verify="" >
                            <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                            @foreach($countries  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
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
                    <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(isset($dateTime)) value="{{$dateTime}}" @endif >
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
                <th  lay-data="{field:'user_id', width:130 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
                <th  lay-data="{field:'user_name', minWidth:150}">{{trans('user.table.header.user_name')}}</th>
                <th  lay-data="{field:'user_phone', minWidth:180}">{{trans('user.table.header.phone')}}</th>
                <th  lay-data="{field:'friends', minWidth:100,sort:true}">{{trans('user.table.header.friend_count')}}</th>
                <th  lay-data="{field:'send', minWidth:120,sort:true}">{{trans('user.table.header.message_send')}}</th>
                <th  lay-data="{field:'receive', minWidth:120,sort:true}">{{trans('user.table.header.message_received')}}</th>
                <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
                <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>
                <th  lay-data="{field:'time', width:160,sort:true}">{{trans('user.table.header.last_active_time')}}</th>
                <th  lay-data="{field:'ip', width:160}">{{trans('user.table.header.last_active_ip')}}</th>
                <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
                {{--                <th  lay-data="{field:'user_op', minWidth:100 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>--}}
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td><img width="32px;" src="{{splitJointQnImageUrl($user->user_avatar)}}" /></td>
                    <td>{{$user->user_nick_name}}</td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_phone_country}} {{$user->user_phone}}</td>
                    <td>{{$user->friends}}</td>
                    <td>{{$user->send}}</td>
                    <td>{{$user->receive}}</td>
                    <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
                    <td>{{ $user->country }}</td>
                    <td>{{$user->time}}</td>
                    <td>{{$user->ip}}</td>
                    <td>{{ $user->user_format_created_at }}</td>
                    {{--                    <td></td>--}}
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
    <!--    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="history">{{trans('common.table.button.detail')}}</a>
        </div>
    </script>-->
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer', 'flow' , 'laydate' , 'timePicker'], function () {
            let $ = layui.jquery,
                table = layui.table,
                flow = layui.flow,
                timePicker = layui.timePicker;
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}"
                },
            });
            table.init('table', {
                page:false,
            });
            flow.lazyimg();

        })
    </script>

    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
