@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this"  lay-id="0">Chat</li>
            <li  lay-id="1">Charts</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
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
                <table class="layui-table"   lay-filter="static_table">
                    <thead>
                    <tr>
                        <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">ID</th>
                        <th  lay-data="{field:'id', width:80}">LogID</th>
                        <th  lay-data="{field:'user_name', width:150}">{{trans('user.table.header.user_name')}}</th>
                        <th  lay-data="{field:'user_nickname', width:190}">{{trans('user.table.header.user_nick_name')}}</th>
                        <th  lay-data="{field:'app_version', width:100}">{{trans('feedback.form.label.app_version')}}</th>
                        <th  lay-data="{field:'system_type', minWidth:100}">{{trans('feedback.form.label.system_type')}}</th>
                        <th  lay-data="{field:'system_version', width:100}">{{trans('feedback.form.label.system_version')}}</th>
                        <th  lay-data="{field:'carriname', width:100}">{{trans('feedback.form.label.carriname')}}</th>
                        <th  lay-data="{field:'domain', minWidth:200}">{{trans('feedback.form.label.domain')}}</th>
                        <th  lay-data="{field:'networking',width:100}">{{trans('feedback.form.label.networking')}}</th>
                        <th  lay-data="{field:'network_type',width:120}">{{trans('feedback.form.label.network_type')}}</th>
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
                            <td>{{$l->domain}}</td>
                            <td>{{$l->networking}}</td>
                            <td>{{$l->network_type}}</td>
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
            </div>
            <div class="layui-tab-item"  id="layui-echarts">
                <div id="container" style="height: 100%">
                </div>
            </div>
        </div>
    </div>
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
            echarts: 'lay/modules/echarts',
        }).use(['element' , 'table', 'laydate' , 'echarts'], function () {
            let $ = layui.jquery,
                element = layui.element,
                table = layui.table,
                echarts = layui.echarts,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });

            table.init('static_table', {
                page:false
            });

            let dom = document.getElementById("container");
            let myChart = echarts.init(dom);
            let option = {
                tooltip: {
                    trigger: 'axis',
                },
                legend: {
                    data: @json($header)
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: @json($dates)
                    }
                ],
                yAxis: [{type: 'value'}],
                series: @json($line)
            };
            if (option && typeof option === 'object') {
                myChart.setOption(option);
                window.onresize = function () {//用于使chart自适应高度和宽度
                    autoContainer();//重置容器高宽
                    myChart.resize();
                };
            }
            let autoContainer = function () {
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                myChart.resize();

            };

            element.on('tab(tab)', function(data){
                if(data.index==1)
                {
                    autoContainer();
                }
            });
            autoContainer();
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
