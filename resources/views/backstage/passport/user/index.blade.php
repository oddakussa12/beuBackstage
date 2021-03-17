@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="0">Users</li>
            <li lay-id="1">Charts</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
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
                                <label class="layui-form-label">{{trans('user.form.phone')}}:</label>
                                <div class="layui-input-inline">
                                    <input class="layui-input" placeholder="fuzzy search" name="phone" id="phone"  @if(!empty($phone)) value="{{$phone}}" @endif />
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
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
                                <label class="layui-form-label">{{trans('user.form.label.user_created_at')}}:</label>
                                <div class="layui-input-inline" style="width: 300px;">
                                    <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                                </div>
                            </div>

                            <div class="layui-inline">
                                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                                <a href="{{route('passport::user.export')}}@if(!empty($query))?{{$query}}@endif" class="layui-btn" target="_blank">{{trans('common.form.button.export')}}</a>
                                <div class="layui-btn layui-btn-primary">{{$users->total()}}</div>
                            </div>
                        </div>
                    </form>
                    <table class="layui-table"  lay-filter="user_table">
                        <thead>
                        <tr>
                            <th  lay-data="{field:'user_id', width:130 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                            <th  lay-data="{field:'user_avatar', width:80}">{{trans('user.table.header.user_avatar')}}</th>
                            <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
                            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
                            <th  lay-data="{field:'user_phone', minWidth:180}">{{trans('user.table.header.phone')}}</th>
                            <th  lay-data="{field:'friends', minWidth:100,sort:true}">{{trans('user.table.header.friend_count')}}</th>
                            <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
                            <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>
                            <th  lay-data="{field:'time', width:160,sort:true}">{{trans('user.table.header.last_avtive_time')}}</th>
                            <th  lay-data="{field:'ip', width:160}">{{trans('user.table.header.last_avtive_ip')}}</th>

                            <th  lay-data="{field:'activation', width:70}">{{trans('user.table.header.user_activation')}}</th>
                            <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
                            <th  lay-data="{field:'user_op', minWidth:200 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->user_id}}</td>
                                <td><img width="32px;" src="@if(stripos($user->user_avatar, 'mantou')===false)https://qnwebothersia.mmantou.cn/{{$user->user_avatar}}@else{{$user->user_avatar}}@endif?imageView2/0/w/32/h/32/interlace/1|imageslim" /></td>
                                <td>{{$user->user_nick_name}}</td>
                                <td>{{$user->user_name}}</td>
                                <td>{{$user->user_phone_country}} {{$user->user_phone}}</td>
                                <td>{{$user->friends}}</td>
                                <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
                                <td>{{ $user->country }}</td>
                                <td>{{$user->time}}</td>
                                <td>{{$user->ip}}</td>
                                <td><span class="layui-btn layui-btn-xs">@if($user->activation==0) YES @else NO @endif</span></td>
                                <td>{{ $user->user_format_created_at }}</td>
                                <td></td>
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
            </div>
            <div class="layui-tab-item"  id="layui-echarts" style="height: 100%;min-height: 400px;">
                <div id="gender" style="width: 50%; min-height: 400px; height: 100%; float: left;"></div>
                <div id="country" style="width: 50%;min-height: 400px; height: 100%; float: left;"></div>
            </div>
        </div>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="friend">{{trans('common.table.button.friend')}}</a>
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="history">{{trans('common.table.button.history')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
            echarts: 'lay/modules/echarts',
        }).use(['element', 'common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker', 'echarts'], function () {
            let $ = layui.jquery,
                element = layui.element,
                table = layui.table,
                common = layui.common,
                echarts = layui.echarts,
                laydate = layui.laydate,
                flow = layui.flow,
                timePicker = layui.timePicker;
            table.init('user_table', { //转化静态表格
                page:false
            });

            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

            table.on('tool(user_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = obj.data; //获得当前行数据
                let layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                let tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'friend'){ //好友
                    layer.open({
                        type: 2,
                        title: "{{trans('user.form.label.friend_list')}}",
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/passport/user/friend/'+data.user_id,
                    });
                }
                if(layEvent === 'history'){ //活跃历史
                    layer.open({
                        type: 2,
                        title: "{{trans('common.table.button.history')}}",
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/passport/user/history/'+data.user_id
                    });                }
            });
            table.init('user_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });

            flow.lazyimg();

            let dom = document.getElementById("gender");
            let myChart = echarts.init(dom);
            let option = {
                    title: {
                        text: 'Sex Distribution Dap',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: "{b}: {c} <br />{d}%"
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                    },
                    series: @json($gender)
            };
            let dom2 = document.getElementById("country");
            let myChart2 = echarts.init(dom2);
            let option2 = {
                title: {
                    text: 'Country Map',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{b}: {c} <br />{d}%"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                },
                series: @json($chartCountry)
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
                myChart2.setOption(option2);
                window.onresize = function () {//用于使chart自适应高度和宽度
                    autoContainer();//重置容器高宽
                    myChart.resize();
                    myChart2.resize();
                };
            }
            let autoContainer = function () {
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                myChart.resize();
                myChart2.resize();
            };

            element.on('tab(tab)', function(data){
                if (data.index==1) {
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
