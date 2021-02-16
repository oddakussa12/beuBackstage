@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-form-label {width: 90px;}
    </style>
    <div  class="layui-fluid">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>{{trans('post.html.post_list')}}</legend>
        </fieldset>
        <table class="layui-table" lay-filter="post_table" id="post_table">

            <thead>
            <tr>
                <th lay-data="{type:'checkbox', fixed: 'left'}"></th>
                <th lay-data="{field:'post_id', width:100}">帖子ID</th>
                <th lay-data="{field:'user_id', width:90}">用户ID</th>
                <th lay-data="{field:'post_uuid', width:300}"><a href="baidu.com">{{trans('post.table.header.post_uuid')}}</a></th>
                <th lay-data="{field:'post_content', width:300}">{{trans('post.table.header.post_content')}}</th>
                <th lay-data="{field:'post_type', width:90}">类型</th>
                <th lay-data="{field:'post_created_at', width:200}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{field:'is_essence', width:100}">{{trans('post.table.header.post_fine')}}</th>
                <th lay-data="{field:'is_preheat', width:100}">{{trans('post.table.header.post_preheat')}}</th>
                <th lay-data="{field:'post_hotting', width:100}">{{trans('post.table.header.post_hotting')}}</th>
                <th lay-data="{field:'post_topping', width:100}">{{trans('post.table.header.post_topping')}}</th>
                <th lay-data="{field:'post_deleted_at', width:120}">{{trans('post.table.header.post_delete')}}</th>
                <th lay-data="{fixed: 'right', minWidth:250, align:'center', toolbar: '#postop'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($posts as $value)
                <tr>
                    <td></td>
                    <td>{{$value->post_id}}</td>
                    <td><a target="_blank" href="https://web.yooul.com/userbox/userHome/userHome_myposts?user_id={{$value->user_id}}">{{$value->user_id}}</a></td>
                    <td title="{{$value->post_uuid}}"><a target="_blank" href="https://web.yooul.com/detail?post_uuid={{$value->post_uuid}}">{{$value->post_uuid}}</a></td>
                    <td lay-tips="{{$value->post_index_title}}" title="{{$value->post_index_title}}">{{$value->post_index_title}}</td>
                    <td>
                        @if ($value->post_type=='vote') <div class="layui-btn layui-btn-sm layui-btn-danger"> 投票帖</div> @endif
                        @if ($value->post_type=='image') <div class="layui-btn layui-btn-sm layui-btn-warm">图片帖</div> @endif
                        @if ($value->post_type=='video') <div class="layui-btn layui-btn-sm layui-btn-normal">视频帖</div> @endif
                        @if ($value->post_type=='text') <div class="layui-btn layui-btn-sm ">文本帖</div> @endif
                    </td>
                    <td>{{$value->post_format_created_at}}</td>
                    <td><input type="checkbox" @if($value->is_essence==1) checked @endif name="is_essence" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td><input type="checkbox" @if($value->is_preheat==1) checked @endif name="is_preheat" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td><input type="checkbox" @if($value->post_hotting==1) checked @endif name="post_hotting" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td><input type="checkbox" @if($value->post_topping==1) checked @endif name="post_topping" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td><input type="checkbox" @if(!empty($value->post_deleted_at)) checked @endif name="delete" lay-skin="switch" lay-filter="switchAll" lay-text="已删除|未删除"></td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $posts->links('vendor.pagination.default') }}
        @else
            {{ $posts->appends($appends)->links('vendor.pagination.default') }}
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
            const form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker,
                $ = layui.jquery;
            table.on('toolbar(post_table)', function(obj){
                const checkStatus = table.checkStatus(obj.config.id);
                let data = checkStatus.data;

                switch(obj.event){
                    case 'getCheckData':
                        data = checkStatus.data;
                        layer.alert(JSON.stringify(data));
                        break;
                    case 'getCheckLength':
                        data = checkStatus.data;
                        layer.msg('选中了：'+ data.length + ' 个');
                        break;
                    case 'isAll':
                        layer.msg(checkStatus.isAll ? '全选': '未全选');
                        break;
                    case 'del':
                        post(data, 'delete');
                        break;
                    case 'recover':
                        post(data, 'recover');
                        break;
                    //自定义头工具栏右侧图标 - 提示
                    case 'LAYTABLE_TIPS':
                        layer.alert('这是工具栏右侧自定义的一个图标按钮');
                        break;
                };
            });
            table.on('tool(post_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                if(layEvent === 'edit'){ //编辑
                    open(['90%','95%'], '/backstage/content/post/'+data.post_id+'/edit')
                }else if(layEvent === 'image'){ //编辑
                    open(['90%','95%'], '/backstage/content/post/'+data.post_id+'/image')
                }else if(layEvent === 'comment'){ //编辑
                    open(['90%','95%'], '/backstage/content/post/'+data.post_id+'/comment')
                }else if(layEvent === 'translation'){ //编辑
                    open(['80%','80%'], '/backstage/content/post/'+data.post_id+'/translation')
                }
            });
            form.on('switch(switchAll)', function(data){
                let params;
                const checked = data.elem.checked;
                const postId  = data.othis.parents('tr').find("td:eq(1)").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('content::post.update'))
                    common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                    form.render();
                    return false;
                @endif
                let name = $(data.elem).attr('name');
                params = checked ? '{"' + name + '":"on"}' : '{"' + name + '":"off"}';
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/content/post')}}/"+postId , JSON.parse(params) , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'put' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });

            table.init('post_table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime', //定义输入框input对象
                options:{      //可选参数timeStamp，format
                    timeStamp:false,//true开启时间戳 开启后format就不需要配置，false关闭时间戳 //默认false
                    format:'YYYY-MM-DD HH:ss:mm',//格式化时间具体可以参考moment.js官网 默认是YYYY-MM-DD HH:ss:mm
                },
            });

            function post(data, type) {
                let arr = [];
                layer.confirm("{{trans('common.confirm.operate')}}", function(){
                    data.forEach(function (data) {
                        arr.push(data.post_id);
                    });
                    common.ajax("{{url('/backstage/content/post/batch')}}" , {"ids":arr, "type":type}, function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.operate')}}" , 1 , 500 , 6 , 't' ,function () {
                            console.log(res);
                            location.reload();
                        });
                    }, 'post');
                });
            }
            function open(area, content) {
                layer.open({
                    type: 2,
                    shadeClose: true,
                    shade: 0.8,
                    area: area,
                    offset: 'auto',
                    content: content,
                });
            }
        });

    </script>
    <script type="text/html" id="toolbar">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <select name="field">
                        <option value="">{{trans('common.form.placeholder.select_first')}}</option>
                        @foreach(trans('post.form.select.query') as $key=>$translation)
                            <option value="{{$key}}" @if(!empty($field)&&$field==$key) selected @endif>{{$translation}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">:</div>

                <div class="layui-input-inline">
                    <input  style="width: 280px;" class="layui-input" name="v" id="value"  @if(!empty($v)) value="{{$v}}" @endif />
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">用户ID:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_id" id="user_id" @if(!empty($user_id)) value="{{$user_id}}" @endif/>
                </div>
                <label class="layui-form-label">发帖人:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="user_name" placeholder="user_name" id="user_name" @if(!empty($user_name)) value="{{$user_name}}" @endif/>
                </div>
            </div>


            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">创建时间:</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
{{--                    <button class="layui-btn" type="button" id="clearCache" >清空缓存</button>--}}
                    <div class="layui-btn layui-btn-primary">{{$posts->total()}}</div>
                </div>

            </div>
        </form>
        <div class="layui-btn-container">
{{--            <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>--}}
            <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>
            <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>
            <button class="layui-btn layui-btn-danger" lay-event="del">删除</button>
            <button class="layui-btn layui-btn-warm" lay-event="recover">取消删除</button>

        </div>
    </script>
    <script type="text/html" id="postop">
{{--        <a class="layui-btn layui-btn-xs" lay-event="check">{{trans('common.table.button.check')}}</a>--}}
        <a class="layui-btn layui-btn-xs" lay-event="image">详情</a>
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="comment">{{trans('post.table.button.comment')}}</a>
        <a class="layui-btn layui-btn-xs" lay-event="translation">{{trans('post.table.button.translation')}}</a>

        {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>--}}
    </script>
@endsection