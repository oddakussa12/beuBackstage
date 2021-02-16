@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>举报历史</legend>
        </fieldset>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_id" id="user_id"  @if(!empty($user_id)) value="{{$user_id}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">被举报人/帖:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="reportable_id" id="reportable_id"  @if(!empty($reportable_id)) value="{{$reportable_id}}" @endif />
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                    <div class="layui-input-inline" style="width: 290px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="user_table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', minWidth:100 ,fixed: 'left'}">序号</th>
                <th  lay-data="{field:'user_id', minWidth:100 ,fixed: 'left'}">举报人ID</th>
                <th  lay-data="{field:'reportable_id', minWidth:150 ,fixed: 'left'}">被举报人/帖</th>
                <th  lay-data="{field:'reportable_type', minWidth:190}">类型</th>
                <th  lay-data="{field:'status', minWidth:100}">状态</th>
                <th  lay-data="{field:'created_at', width:160}">举报时间</th>
                <th  lay-data="{field:'updated_at', width:160}">修改时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$user->user_id}}">{{$user->user_id}}</a></td>
                    <td>@if(stripos($user->reportable_type, 'post'))
                            @if(empty($user->post_uuid))
                                {{$user->reportable_id}}
                            @else
                            <a target="_blank" href="https://web.yooul.com/detail?post_uuid={{$user->post_uuid}}">{{$user->post_uuid}}</a>
                            @endif
                        @else
                            <a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$user->reportable_id}}">{{$user->reportable_id}}</a>
                            @endif
                        </td>
                    <td>{{$user->reportable_type}}</td>
                    <td>{{$user->status}}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td>
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
@endsection
