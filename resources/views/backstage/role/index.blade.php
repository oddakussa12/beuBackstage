@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:100}">{{trans('role.table.header.id')}}</th>
                <th  lay-data="{field:'name', width:200}">{{trans('role.table.header.name')}}</th>
                <th  lay-data="{field:'role_permission', hide:true}">{{trans('role.table.header.name')}}</th>
                <th  lay-data="{field:'guard_name', width:200}">{{trans('role.table.header.guard_name')}}</th>
                <th  lay-data="{field:'created_at', width:200}">{{trans('role.table.header.created_at')}}</th>
                <th  lay-data="{field:'updated_at', width:200}">{{trans('role.table.header.updated_at')}}</th>
                <th  lay-data="{field:'role_op', minWidth:150 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{$role->id}}</td>
                <td>{{$role->name}}</td>
                <td>{{json_encode(array_column($role->permissions->toArray() , 'id'))}}</td>
                <td>{{$role->guard_name}}</td>
                <td>{{$role->created_at}}</td>
                <td>{{$role->updated_at}}</td>
                <td></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $roles->links('vendor.pagination.default') }}

        <form class="layui-form layui-tab-content"  lay-filter="role_form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-block">
                        <input type="hidden" name="id" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('role.form.label.name')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="name"  placeholder="{{trans('role.form.placeholder.name')}}" lay-verify="name" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('role.form.label.role_permission')}}</label>
                    <div class="layui-input-block">
                        <div id="permission_tree" ></div>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="form_submit_add">{{trans('common.form.button.add')}}</button>
                    <button class="layui-btn" lay-submit lay-filter="form_submit_update">{{trans('common.form.button.update')}}</button>
                    <button type="reset" class="layui-btn layui-btn-primary">{{trans('common.form.button.reset')}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
        base: "{{url('plugin/layui')}}/"
        }).extend({
        common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer' , 'tree'], function () {
            var $ = layui.jquery,
            table = layui.table,
            form = layui.form,
            tree = layui.tree,
            common = layui.common,
            layer = layui.layer;




            var permission_data = [
            @foreach($sortPermissions as $k=>$permission)
                {
                label: "{{$k}}"
                ,id: "{{$k}}"
                ,spread:false
                ,children: [
                    @foreach($permission as $sub_permission)
                    {
                        label: "{{$sub_permission['name']}}"
                        ,id: "{{$sub_permission['id']}}"
                    },
                    @endforeach
                ]
            },
        @endforeach
            ];

            tree.render({
                elem: '#permission_tree'
                ,data: permission_data
                ,showCheckbox: true  //是否显示复选框
                ,key: 'id'  //定义索引名称
                ,accordion: true //是否开启手风琴模式
                ,id:'permission_tree'
            });
            table.init('table', { //转化静态表格
                page:false
            });
            table.on('tool(table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/role')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');


                        //向服务端发送删除指令
                    });
                } else if(layEvent === 'edit'){ //编辑

                    form.val("role_form", {
                        "id": data.id // "name": "value"
                        ,"name": data.name
                    });
                    tree.reload('permission_tree');
                    tree.setChecked('permission_tree', JSON.parse(data.role_permission));

                }else if(layEvent === 'menu_toggle')
                {
                    treetable.toggleRows($(this).find('.treeTable-icon'), false);
                }
            });
            form.on('submit(form_submit_add)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined)
                    {
                        return true;
                    }
                    params[k] = v;
                });
                var  checkedData = tree.getChecked('permission_tree');
                $.each(checkedData , function (key , value) {
                    $.each(value.children , function (k ,v) {
                        if(v.id==''||v.id==undefined||v.id==undefined)
                        {
                            return true;
                        }
                        var auth = v.label;
                        var id = parseInt(v.id);
                        if(params.hasOwnProperty('role_auth'))
                        {
                            params['role_auth'][id] = auth;
                        }else{
                            params['role_auth'] = {};
                            params['role_auth'][id] = auth;
                        }
                    });
                });
                common.ajax("{{url('/backstage/role')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                        location.reload();
                    });
                });
                return false;
            });
            form.on('submit(form_submit_update)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined)
                    {
                        return true;
                    }
                    params[k] = v;
                });
                var i=0;
                var  checkedData = tree.getChecked('permission_tree');
                $.each(checkedData , function (key , value) {
                    $.each(value.children , function (k ,v) {
                        if(v.id==''||v.id==undefined||v.id==undefined)
                        {
                            return true;
                        }
                        var auth = v.label;
                        var id = parseInt(v.id);
                        if(params.hasOwnProperty('role_auth'))
                        {
                            params['role_auth'][i] = auth;
                        }else{
                            params['role_auth'] = {};
                            params['role_auth'][i] = auth;
                        }
                        i++;
                    });
                });
                common.ajax("{{url('/backstage/role/')}}/"+params.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });
            form.on('submit(role_form)', function(){
                return false;
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
