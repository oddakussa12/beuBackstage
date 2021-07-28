@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'id', width:100}">{{trans('permission.table.header.id')}}</th>
                <th  lay-data="{field:'name', width:300}">{{trans('permission.table.header.name')}}</th>
                <th  lay-data="{field:'guard_name', width:200}">{{trans('permission.table.header.guard_name')}}</th>
                <th  lay-data="{field:'created_at', width:200}">{{trans('permission.table.header.created_at')}}</th>
                <th  lay-data="{field:'updated_at', width:200}">{{trans('permission.table.header.updated_at')}}</th>
                <th  lay-data="{field:'permission_op', minWidth:120 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{$permission->id}}</td>
                    <td>{{$permission->name}}</td>
                    <td>{{$permission->guard_name}}</td>
                    <td>{{$permission->created_at}}</td>
                    <td>{{$permission->updated_at}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $permissions->links('vendor.pagination.default') }}

        <form class="layui-form layui-tab-content"  lay-filter="permission_form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-block">
                        <input type="hidden" name="id" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('permission.form.label.name')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="name"  placeholder="{{trans('permission.form.placeholder.name')}}" lay-verify="name" autocomplete="off" class="layui-input">
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
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common;
            table.init('table', {
                page:false
            });

            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'del'){
                    common.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/permission')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                } else if(layEvent === 'edit'){
                    form.val("permission_form", {
                        "id": data.id
                        ,"name": data.name
                    });

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

                common.ajax("{{url('/backstage/permission')}}" , params , function(res){
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

                common.ajax("{{url('/backstage/permission/')}}/"+params.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
                return false;
            });
            form.on('submit(role_form)', function(){
                return false;
            });
        })
    </script>
@endsection
