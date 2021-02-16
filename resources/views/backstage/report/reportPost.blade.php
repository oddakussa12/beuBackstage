@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">排序：</label>
                    <div class="layui-input-inline">
                        <select name="order">
                            <option value=""  @if(empty($order))  selected @endif>时间</option>
                            <option value="1" @if(!empty($order)) selected @endif>次数</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">处理状态：</label>
                    <div class="layui-input-inline">
                        <select name="status">
                            <option value="0" @if(isset($status) && $status==0)  selected @endif>未处理</option>
                            <option value="1" @if(!empty($status)) selected @endif>已处理</option>
                            <option value="" @if(!isset($status))  selected @endif>全部</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">帖子状态：</label>
                    <div class="layui-input-inline">
                        <select name="is_delete">
                            <option value=""  @if(isset($is_delete) && $is_delete=='') selected @endif>全部</option>
                            <option value="0" @if(isset($is_delete) && $is_delete==0)  selected @endif>未删除</option>
                            <option value="1" @if(isset($is_delete) && $is_delete==1)  selected @endif>已删除</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">举报类型：</label>
                    <div class="layui-input-inline">
                        <select name="type">
                            <option value=""     @if(empty($type))  selected @endif>全部</option>
                            <option value="user" @if(!empty($type) && $type=='user') selected @endif>举报用户</option>
                            <option value="post" @if(!empty($type) && $type=='post') selected @endif>举报帖子</option>
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="user_id" id="user_id"  @if(!empty($user_id)) value="{{$user_id}}" @endif />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">被举报人:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="reportable_id" id="reportable_id"  @if(!empty($reportable_id)) value="{{$reportable_id}}" @endif />
                    </div>
                </div>

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
        <table class="layui-table" lay-filter="post_table" id="post_table">
            <thead>
            <tr>
                <th lay-data="{field:'post_id', width:100}">帖子ID</th>
                <th lay-data="{field:'user_id', width:90}">用户ID</th>
                <th lay-data="{field:'post_uuid', width:300}">{{trans('post.table.header.post_uuid')}}</th>
                <th lay-data="{field:'post_type', width:90}">类型</th>
                <th lay-data="{field:'num_all', width:100}">被举报次数</th>
                <th lay-data="{field:'created_at', width:160}">第一次被举报时间</th>
                <th lay-data="{field:'post_created_at', width:200}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'post_hotting', width:100}">{{trans('post.table.header.post_hotting')}}</th>
                <th lay-data="{field:'post_topping', width:100}">{{trans('post.table.header.post_topping')}}</th>
                <th lay-data="{field:'post_deleted_at', width:120}">{{trans('post.table.header.post_delete')}}</th>
                <th lay-data="{field:'post_op', minWidth:200 ,fixed: 'right'}">{{trans('common.table.header.op')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($users as $value)
                <tr>
                    <td><a target="_blank" href="{{route('content::post.index')}}?field=post_uuid&v={{$value->post_uuid}}">{{$value->post_id}}</a></td>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$value->user_id}}">{{$value->user_id}}</a></td>
                    <td title="{{$value->post_uuid}}"><a target="_blank" href="https://web.yooul.com/detail?post_uuid={{$value->post_uuid}}">{{$value->post_uuid}}</a></td>
                    <td>
                        @if ($value->post_type=='vote') <div class="layui-btn layui-btn-sm layui-btn-danger"> 投票帖</div> @endif
                        @if ($value->post_type=='image') <div class="layui-btn layui-btn-sm layui-btn-warm">图片帖</div> @endif
                        @if ($value->post_type=='video') <div class="layui-btn layui-btn-sm layui-btn-normal">视频帖</div> @endif
                        @if ($value->post_type=='text') <div class="layui-btn layui-btn-sm ">文本帖</div> @endif
                    </td>
                    <td>{{$value->num_all}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>{{$value->post_created_at}}</td>
                    <td>
                        <input type="checkbox" @if($value->post_hotting==1) checked @endif name="post_hotting" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">
                    </td>
                    <td>
                        <input type="checkbox" @if($value->post_topping==1) checked @endif name="post_topping" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF">
                    </td>
                    <td>
                        <input type="checkbox" @if(!empty($value->post_deleted_at)) checked @endif name="delete" lay-skin="switch" lay-filter="switchAll" lay-text="已删除|未删除">
                    </td>
                    <td>
                        <div class="layui-table-cell laytable-cell-1-6">
                            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="people" target="_blank" href="{{route('report::report.history')}}?details=1&type=post&reportable_id={{$value->reportable_id}}">被举报明细</a>
                            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="people" target="_blank" href="{{route('report::report.reportUser')}}?reportable_id={{$value->reportable_id}}">举报人</a>
                        </div>
                    </td>
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
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $=layui.jquery;
            table.on('tool(post_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象
                if(layEvent === 'check'){ //查看
                    //do somehing
                    alert('check');
                } else if(layEvent === 'image'){ //编辑
                    var post_id = data.post_id;
                    layer.open({
                        type: 2,
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%','95%'],
                        offset: 'auto',
                        content: '/backstage/content/post/'+post_id+'/image',
                    });
                }
            });


            table.init('post_table', { //转化静态表格
                page:false,
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

        });
    </script>

@endsection