@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="0">Users</li>
            <li lay-id="1">Charts</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
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
                    </div>
                    <div class="layui-form-item">
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
                            <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                            <div class="layui-input-inline" >
                                <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                            </div>
                            <div class="layui-input-inline">
                                <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                                <a href="{{route('passport::user.export')}}@if(!empty($query))?{{$query}}@endif" class="layui-btn" target="_blank">{{trans('common.form.button.export')}}</a>
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
                        <th  lay-data="{field:'user_phone', minWidth:180}">{{trans('user.table.header.phone')}}</th>
                        <th  lay-data="{field:'friends', minWidth:120,sort:true}">{{trans('user.table.header.friend_count')}}</th>
                        <th  lay-data="{field:'user_is_block', width:100}">{{trans('user.table.header.user_block')}}</th>
                        <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
                        <th  lay-data="{field:'country', width:80}">{{trans('user.table.header.user_country')}}</th>

                        <th  lay-data="{field:'activation', width:70}">{{trans('user.table.header.user_activation')}}</th>
                        <th  lay-data="{field:'user_created_at', width:160}">{{trans('user.table.header.user_registered')}}</th>
                        <th  lay-data="{field:'user_op', minWidth:480 ,fixed: 'right', templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
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
                            <td><input type="checkbox" @if($user->is_block==true) checked @endif name="is_block" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                            <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
                            <td>{{ $user->country }}</td>

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
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="device">Device</a>
            <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="friend">{{trans('user.table.button.friend')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="history">{{trans('user.table.button.history')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="friend_active">{{trans('user.table.button.friend_active')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="friend_yesterday_active">{{trans('user.table.button.friend_yesterday_active')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="follow">{{trans('user.table.button.follow')}}</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="followed">{{trans('user.table.button.followed')}}</a>
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
                form = layui.form,
                flow = layui.flow,
                timePicker = layui.timePicker;

            timePicker.render({
                elem: '#dateTime', //???????????????input??????
                options:{      //????????????timeStamp???format
                    timeStamp:false,//true??????????????? ?????????format?????????????????????false??????????????? //??????false
                    format:'YYYY-MM-DD HH:ss:mm',//?????????????????????????????????moment.js?????? ?????????YYYY-MM-DD HH:ss:mm
                    locale:"{{locale()}}"
                },
            });
            form.on('switch(switchAll)', function(data){
                let checked = data.elem.checked;
                data.elem.checked = !checked;

                const name = $(data.elem).attr('name');
                let id = data.othis.parents('tr').find("td :first").text();
                const url = "{{LaravelLocalization::localizeUrl('/backstage/passport/user')}}/"+id;

                const params = {name: name, 'value': checked ? 1 : 0, 'user_id': id};

                @if(!Auth::user()->can('passport::user.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                form.render();
                console.log(params);
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax(url , params , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt(res.result , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                let data = obj.data; //?????????????????????
                let layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                let tr = obj.tr; //??????????????? tr ???DOM??????
                if(layEvent === 'friend'){ //??????
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
                }else if(layEvent === 'history'){ //????????????
                    layer.open({
                        type: 2,
                        title: "{{trans('user.table.button.history')}}",
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/passport/user/history/'+data.user_id
                    });
                }else if(layEvent === 'device'){ //????????????
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/passport/user/device/'+data.user_id
                    });
                }else if(layEvent === 'friend_active'){
                    window.open('/backstage/passport/user/'+data.user_id+'/friend/status');
                }else if(layEvent === 'friend_yesterday_active'){
                    window.open('/backstage/passport/user/'+data.user_id+'/friend/yesterday/status');
                }else if(layEvent === 'follow'){
                    window.open('/backstage/passport/follow?follow_id='+data.user_id);
                }else if(layEvent === 'followed'){
                    window.open('/backstage/passport/follow?followed_id='+data.user_id);
                }
            });
            table.init('table', { //??????????????????
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
                window.onresize = function () {//?????????chart????????????????????????
                    autoContainer();//??????????????????
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
@endsection
