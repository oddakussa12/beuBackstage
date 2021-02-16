@extends('layouts.dashboard')
@section('layui-content')
    <div style="padding: 20px;">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header">app统计</div>
                            <div class="layui-card-body">
                                <form class="layui-form">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">日期：</label>
                                        <div class="layui-input-block">
                                            @if($type=='app')
                                                <input type="text" class="layui-input" name="dateTime" id="app"  placeholder="yyyy-MM-dd" value="{{$dateTime}}">
                                            @else
                                                <input type="text" class="layui-input" name="dateTime" id="app"  placeholder="yyyy-MM-dd" value="{{$defaultDate}}">
                                            @endif
                                        </div>
                                        <input type="hidden" name="type"  value="app">

                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">在线时长：</label>
                                        @foreach($app as $p)
                                            @if(isset($p['duration']))
                                            <button type="button" class="layui-btn"><span class="layui-badge layui-bg-orange">{{$p['duration']}}</span> </button>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">新增：</label>
                                        @foreach($app as $p)
                                            <button type="button" class="layui-btn"><span class="layui-badge layui-bg-orange">{{$p['newUsers']}}</span> </button>
                                        @endforeach
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">总共：</label>
                                        @foreach($app as $p)
                                            <button type="button" class="layui-btn"><span class="layui-badge layui-bg-orange">{{$p['totalUsers']}}</span> </button>
                                        @endforeach
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">活跃：</label>
                                        @foreach($app as $p)
                                            <button type="button" class="layui-btn"><span class="layui-badge layui-bg-orange">{{$p['activityUsers']}}</span> </button>
                                        @endforeach
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">启动：</label>
                                        @foreach($app as $p)
                                            <button type="button" class="layui-btn"><span class="layui-badge layui-bg-orange">{{$p['launches']}}</span> </button>
                                        @endforeach
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit="" >查询</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header">新增用户</div>
                            <div class="layui-card-body">
                                <form class="layui-form">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">日期：</label>
                                        <div class="layui-input-block">
                                            @if($type=='newUser')
                                                <input type="text" class="layui-input" name="dateTime" id="newUser"  placeholder="yyyy-MM-dd" value="{{$dateTime}}">
                                            @else
                                                <input type="text" class="layui-input" name="dateTime" id="newUser"  placeholder="yyyy-MM-dd" value="{{$defaultDiffDate}}">
                                            @endif
                                        </div>
                                        <input type="hidden" name="type"   value="newUser">

                                    </div>
                                    @if(!empty($newUser[0])&&!empty($newUser[1]))
                                        @foreach($newUser[0] as $k=>$daily)
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">日期：</label>
                                                <button type="button" class="layui-btn">{{$daily['date']}}=><span class="layui-badge layui-bg-orange">{{$daily['value']}}</span> </button>
                                                <button type="button" class="layui-btn">{{$newUser[1][$k]['date']}}=><span class="layui-badge layui-bg-orange">{{$newUser[1][$k]['value']}}</span> </button>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit="" >查询</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header">昨天和今天的数据</div>
                            <div class="layui-card-body">
                                <form class="layui-form">
                                    <div class="layui-form-item">
                                        <input type="hidden" name="type"  value="yt">
                                    </div>
                                    @foreach($yt as $k=>$y)
                                        @foreach($y as $daily)
                                            <div class="layui-form-item">
                                                <label class="layui-form-label"> {{$daily['date']}}@if($k==0) (ios) @elseif($k==1)  (android) @endif：</label>
                                                <button type="button" class="layui-btn">新增:<span class="layui-badge layui-bg-orange">{{$daily['newUsers']}}</span> </button>
                                                <button type="button" class="layui-btn">总共:<span class="layui-badge layui-bg-orange">{{$daily['totalUsers']}}</span> </button>
                                                <button type="button" class="layui-btn">活跃:<span class="layui-badge layui-bg-orange">{{$daily['activityUsers']}}</span> </button>
                                                <button type="button" class="layui-btn">启动:<span class="layui-badge layui-bg-orange">{{$daily['launches']}}</span> </button>
                                            </div>
                                        @endforeach
                                    @endforeach
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit="" >查询</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
{{--                    <div class="layui-col-md6">--}}
{{--                        <div class="layui-card">--}}
{{--                            <div class="layui-card-header">启动次数</div>--}}
{{--                            <div class="layui-card-body">--}}
{{--                                <form class="layui-form">--}}
{{--                                    <div class="layui-form-item">--}}
{{--                                        <label class="layui-form-label"></label>--}}
{{--                                        <div class="layui-input-block">--}}
{{--                                            @if($type=='startTurn')--}}
{{--                                                <input type="text" class="layui-input" name="dateTime" id="startTurn"  placeholder="yyyy-MM-dd" value="{{$dateTime}}">--}}
{{--                                            @else--}}
{{--                                                <input type="text" class="layui-input" name="dateTime" id="startTurn"  placeholder="yyyy-MM-dd" value="{{$defaultDiffDate}}">--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                        <input type="hidden" name="type"   value="startTurn">--}}

{{--                                    </div>--}}
{{--                                    @foreach($startTurn as $daily)--}}
{{--                                        <div class="layui-form-item">--}}
{{--                                            <label class="layui-form-label">日期：</label>--}}
{{--                                            <button type="button" class="layui-btn">{{$daily['date']}}=><span class="layui-badge layui-bg-orange">{{$daily['value']}}</span> </button>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}

{{--                                    <div class="layui-form-item">--}}
{{--                                        <div class="layui-input-block">--}}
{{--                                            <button class="layui-btn" lay-submit="" >查询</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
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
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer,
                flow = layui.flow,
                laydate = layui.laydate;

            laydate.render({
                elem: '#dailyNumber'
                ,range: true

            });
            laydate.render({
                elem: '#startTurn'
                ,range: true

            });
            laydate.render({
                elem: '#newUser'
                ,range: true

            });
            laydate.render({
                elem: '#duration'

            });
            laydate.render({
                elem: '#app'

            });


            table.init('user_table', { //转化静态表格
                page:false
            });

            table.on('tool(user_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/permission')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');


                        //向服务端发送删除指令
                    });
                } else if(layEvent === 'edit'){ //编辑
                    location.href='/{{app()->getLocale()}}/backstage/passport/user/'+data.user_id+'/edit';
                }
            });
            flow.lazyimg();

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
