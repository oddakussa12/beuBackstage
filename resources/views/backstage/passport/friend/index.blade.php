@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this"  lay-id="0">Friends</li>
            <li  lay-id="1">Charts</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-fluid">
                    <form class="layui-form">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                                <div class="layui-input-inline">
                                    <select  name="country_code" lay-verify=""   readonly>
                                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                                        @foreach($countries  as $country)
                                            <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('user.table.header.friend_count')}}:</label>
                                <div class="layui-input-inline">
                                    <select  name="num" lay-verify="" readonly>
                                        <option value="0">All</option>
                                        @for($i=1;$i<=10;$i++)
                                            <option value="{{$i}}" @if(isset($num)&&$num==$i) selected @endif>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                                <div class="layui-input-inline">
                                    <select  name="sort" readonly>
                                        <option value="">Default</option>
                                        <option value="friend" @if(!empty($sort)&&$sort=='friend') selected @endif>Friend</option>
                                    </select>
                                </div>
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
                            <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
                            <th  lay-data="{field:'user_phone', minWidth:180}">{{trans('user.table.header.phone')}}</th>
                            <th  lay-data="{field:'friends', minWidth:100,sort:true}">{{trans('user.table.header.friend_count')}}</th>
                            <th  lay-data="{field:'country', minWidth:100}">{{trans('user.table.header.user_country')}}</th>
                            <th  lay-data="{field:'user_gender', width:70}">{{trans('user.table.header.user_gender')}}</th>
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
                                <td>{{$user->country}}</td>
                                <td><span class="layui-btn layui-btn-xs @if($user->user_gender==0) layui-btn-danger @elseif($user->user_gender==1) layui-btn-warm @endif">@if($user->user_gender==-1){{trans('common.cast.sex.other')}}@elseif($user->user_gender==0){{trans('common.cast.sex.female')}}@else{{trans('common.cast.sex.male')}}@endif</span></td>
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
            <div class="layui-tab-item"  id="layui-echarts">
                <div id="container" style="height: 100%">
                </div>
            </div>
        </div>
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
        }).use(['common' , 'table' , 'layer' , 'element' , 'element', 'flow' , 'echarts'], function () {
            let $ = layui.jquery,
                table = layui.table,
                flow = layui.flow,
                echarts = layui.echarts,
                element = layui.element;
            table.init('table', { //??????????????????
                page:false
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
                }
                if(layEvent === 'history'){ //????????????
                    layer.open({
                        type: 2,
                        title: "{{trans('user.table.button.history')}}",
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%','90%'],
                        offset: 'auto',
                        'scrollbar':true,
                        content: '/backstage/passport/user/history/'+data.user_id
                    });                }
            });
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });

            flow.lazyimg();

            let dom = document.getElementById("container");
            let myChart = echarts.init(dom);

            let option = {
                tooltip: {
                    trigger: 'axis',
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: @json($dates)
                },
                yAxis: {
                    type: 'value'
                },
                series: @json($line)
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
                window.onresize = function () {//?????????chart????????????????????????
                    autoContainer();//??????????????????
                    myChart.resize();
                };
            }
            let autoContainer = function () {
                console.log($('.layui-body').height());
                $('#layui-echarts').height($('.layui-body').height()-200);
                myChart.resize();
            };
            element.on('collapse(tab)', function(data){
            });

            element.on('tab(tab)', function(data){
                if(data.index==1)
                {
                    autoContainer();
                }
            });
        })
    </script>
@endsection
