@extends('layouts.dashboard')
@section('layui-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" placeholder="fuzzy search" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-label">{{trans('feedback.form.label.app_version')}}:</label>
                <div class="layui-input-inline">
                    <select  name="app_version" lay-verify="">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach($appVersions as $s)
                            <option value="{{$s}}" @if(!empty($app_version)&&$app_version==$s) selected @endif>{{$s}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('feedback.form.label.networking')}}:</label>
                <div class="layui-input-inline">
                    <select  name="networking" lay-verify="">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach($networks as $val)
                            <option value="{{$val}}" @if(!empty($networking)&&$networking==$val) selected @endif>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('feedback.form.label.system_version')}}:</label>
                <div class="layui-input-inline">
                    <select  name="system_version" lay-verify="">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach($systemVersions as $v)
                            <option value="{{$v}}" @if(!empty($system_version)&&$system_version==$v) selected @endif>{{$v}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('feedback.form.label.network_type')}}:</label>
                <div class="layui-input-inline">
                    <select  name="network_type" lay-verify="">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach($networkTypes as $item)
                            <option value="{{$item}}" @if(!empty($network_type)&&$network_type==$item) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder="yyyy-MM-dd - yyyy-MM-dd" @if(!empty($dateTime))value="{{$dateTime}}"@endif>
                </div>
            </div>

            <div class="layui-inline">
                <button class="layui-btn" type="submit" lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </div>
    </form>
    <table class="layui-table"   lay-filter="table">
        <thead>
        <tr>
            <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">ID</th>
            <th  lay-data="{field:'id', width:80}">LogID</th>
            <th  lay-data="{field:'user_name', width:150}">{{trans('user.table.header.user_name')}}</th>
            <th  lay-data="{field:'user_nickname', width:190}">{{trans('user.table.header.user_nick_name')}}</th>
            <th  lay-data="{field:'app_version', width:100}">{{trans('feedback.form.label.app_version')}}</th>
            <th  lay-data="{field:'system_type', minWidth:160}">{{trans('feedback.form.label.system_type')}}</th>
            <th  lay-data="{field:'system_version', width:100}">{{trans('feedback.form.label.system_version')}}</th>
            <th  lay-data="{field:'carriname', width:100}">{{trans('feedback.form.label.carriname')}}</th>
            <th  lay-data="{field:'remote_domain', minWidth:200}">{{trans('feedback.form.label.domain')}}</th>
            <th  lay-data="{field:'networking',width:100}">{{trans('feedback.form.label.networking')}}</th>
            <th  lay-data="{field:'network_type',width:120}">{{trans('feedback.form.label.network_type')}}</th>
            <th  lay-data="{field:'real_ip',width:130}">{{trans('user.table.header.user_ip_address')}}</th>
            <th  lay-data="{field:'local_ip',width:130}">{{trans('feedback.form.label.local_ip')}}</th>
            <th  lay-data="{field:'local_gateway',width:130}">{{trans('feedback.form.label.local_gateway')}}</th>
            <th  lay-data="{field:'local_dns',width:130}">{{trans('feedback.form.label.local_dns')}}</th>
            <th  lay-data="{field:'dns_result',width:180}">{{trans('feedback.form.label.dns_result')}}</th>
            <th  lay-data="{field:'tcp_connect_test', width:200}">{{trans('feedback.form.label.tcp_connect_test')}}</th>
            <th  lay-data="{field:'ping', minWidth:200}">{{trans('feedback.form.label.ping')}}</th>
            <th  lay-data="{field:'created_at', width:160}">{{trans('user.table.header.user_time')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $l)
            <tr>
                <td>{{$l->user_id}}</td>
                <td>{{$l->id}}</td>
                <td>{{$l->user_name}}</td>
                <td>{{$l->user_nick_name}}</td>
                <td>{{$l->app_version}}</td>
                <td>{{$l->system_type}}</td>
                <td>{{$l->system_version}}</td>
                <td>{{$l->carriname}}</td>
                <td>{{$l->remote_domain}}</td>
                <td>{{$l->networking}}</td>
                <td>{{$l->network_type}}</td>
                <td>{{$l->real_ip}}</td>
                <td>{{$l->local_ip}}</td>
                <td>{{$l->local_gateway}}</td>
                <td>{{$l->local_dns}}</td>
                <td>{{$l->dns_result}}</td>
                <td>{{$l->tcp_connect_test}}</td>
                <td>{{$l->ping}}</td>
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
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element' , 'table', 'laydate'], function () {
            let table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });
            table.init('table', {
                page:false
            });
        })
    </script>
@endsection
