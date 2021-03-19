@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this"  lay-id="0">Chat</li>
            <li  lay-id="1">Charts</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-fluid">
                    <form class="layui-form" action="" lay-filter="keep">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('user.form.label.user_name')}}:</label>
                                <div class="layui-input-inline">
                                    <input class="layui-input" placeholder="fuzzy search" name="keyword" id="keyword"  @if(!empty($keyword)) value="{{$keyword}}" @endif />
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">School:</label>
                                <div class="layui-input-inline">
                                    <select  name="school" lay-verify="" lay-search  id="school">
                                        <option value="">{{trans('user.form.placeholder.label.user_school')}}</option>
                                        @foreach($schools  as $s)
                                            <option value="{{$s}}" @if(!empty($school)&&$school==$s) selected @endif>{{$s}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('comment.form.placeholder.comment_country_id')}}:</label>
                                <div class="layui-input-inline">
                                    <select  name="country_code" lay-verify="" lay-search  >
                                        <option value="">{{trans('comment.form.placeholder.comment_country_id')}}</option>
                                        @foreach($countries  as $country)
                                            <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
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
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{trans('user.table.header.user_name')}}</th>
                            <th>{{trans('user.table.header.user_nick_name')}}</th>
                            <th>Video</th>
                            <th>Chat Num</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>{{trans('user.table.header.user_country')}}</th>
                            <th>{{trans('user.table.header.user_school')}}</th>
                            <th>{{trans('user.table.header.user_time')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $l)
                            <tr>
                                <td>{{$l->user_id}}</td>
                                <td>{{$l->user_name}}</td>
                                <td>{{$l->user_nick_name}}</td>
                                <td>{{$l->video}}</td>
                                <td>{{$l->num}}</td>
                                <td>{{$l->amount}}</td>
                                <td>{{$l->type}}</td>
                                <td>{{$l->country}}</td>
                                <td>{{$l->school}}</td>
                                <td>{{$l->time}}</td>
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
        }).use(['element','common' , 'table', 'laydate' , 'echarts'], function () {
            let $ = layui.jquery,
                element = layui.element,
                table = layui.table,
                common = layui.common,
                echarts = layui.echarts,
                laydate = layui.laydate;
            laydate.render({
                elem: '#dateTime'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
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
