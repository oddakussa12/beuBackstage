@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'admin_id', width:100}">{{trans('admin.table.header.admin_id')}}</th>
                <th  lay-data="{field:'admin_auth', hide:true}"></th>
                <th  lay-data="{field:'admin_username', width:100}">{{trans('admin.table.header.admin_username')}}</th>
                <th  lay-data="{field:'admin_email', width:160}">{{trans('admin.table.header.admin_email')}}</th>
                <th  lay-data="{field:'admin_sex', templet: function(field){
                    @php
                        $sex_list = config("common.sex");
                        $sex_list = array_map(function($v){
                            return trans($v);
                        } , $sex_list);
                    @endphp
                    var sex_list = JSON.parse('{{json_encode($sex_list)}}');
                    return '<span>'+ sex_list[field.admin_sex] +'</span>';
                } , width:80 }">{{trans('admin.table.header.admin_sex')}}</th>
                <th  lay-data="{field:'admin_country', width:160}">{{trans('user.table.header.user_country')}}</th>
                <th  lay-data="{field:'admin_status',templet:function(field){
                    @php
                        $status_list = config("common.status");
                        $status_list = array_map(function($v){
                            return trans($v);
                        } , $status_list);
                    @endphp
                    var status = JSON.parse('{{json_encode($status_list)}}');
                    return '<span>'+ status[field.admin_status] +'</span>';
                }, width:80}">{{trans('admin.table.header.admin_status')}}</th>
                <th  lay-data="{field:'admin_roles',templet: function(field){
                    var str = '';
                    var admin_roles = JSON.parse(field.admin_roles);
                    layui.each(admin_roles , function(k ,v){
                        str+=v+';';
                    });
                    return str;
                } , width:200}">{{trans('admin.table.header.admin_role')}}</th>
                <th  lay-data="{field:'admin_realname', width:100}">{{trans('admin.table.header.admin_name')}}</th>
{{--                <th  lay-data="{field:'admin_created_at', width:180}">{{trans('admin.table.header.admin_created_at')}}</th>--}}
                <th  lay-data="{field:'admin_op', minWidth:150 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($admins as $admin)
            <tr>
                <td>{{$admin->admin_id}}</td>
                <td>{{$admin->admin_permissions}}</td>
                <td>{{$admin->admin_username}}</td>
                <td>{{$admin->admin_email}}</td>
                <td>{{$admin->admin_sex}}</td>
                <td>{{$admin->admin_country}}</td>
                <td>{{$admin->admin_status}}</td>
                <td>{{$admin->admin_roles}}</td>
                <td>{{$admin->admin_realname}}</td>
{{--                <td>{{$admin->admin_created_at}}</td>--}}
                <td></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $admins->links('vendor.pagination.default') }}

        <form class="layui-form layui-tab-content"  lay-filter="admin_form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-block">
                        <input type="hidden" name="admin_id" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_email')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_email"  placeholder="{{trans('admin.form.placeholder.admin_email')}}" lay-verify="admin_email" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_username')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_username"  placeholder="{{trans('admin.form.placeholder.admin_username')}}" lay-verify="admin_username" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_realname')}}</label>
                    <div class="layui-input-block">
                        <input type="text" name="admin_realname"  placeholder="{{trans('admin.form.placeholder.admin_realname')}}" lay-verify="admin_realname" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.table.header.admin_country')}}</label>
                    <div class="layui-input-block">
                        <select  name="admin_country" lay-verify="" lay-search  >
                            <option value="">{{trans('admin.form.placeholder.admin_country')}}</option>
                            @foreach($countries  as $country)
                                <option value="{{$country['code']}}" @if(!empty($country_code)&&$country_code==$country['code']) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_sex')}}</label>
                    <div class="layui-input-block">
                        <select name="admin_sex" >
                            <option value="">-{{trans('common.form.placeholder.select_first')}}-</option>
                            @php
                                $sex_list = config('common.sex');
                                krsort($sex_list);
                            @endphp
                            @foreach($sex_list as $k=>$sex)
                                <option id="sex_class_id_{{$k}}" value="{{$k}}" >{{trans($sex)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_status')}}</label>
                    <div class="layui-input-block">
                        <select name="admin_status" >
                            <option value="">-{{trans('common.form.placeholder.select_first')}}-</option>
                            @php
                                $status_list = config('common.status');
                                krsort($status_list);
                            @endphp
                            @foreach($status_list as $k=>$status)
                                <option id="status_class_id_{{$k}}" value="{{$k}}" >{{trans($status)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_role')}}</label>
                    <div class="layui-input-block">
                        <select name="admin_roles"   xm-select="admin_roles" xm-select-search="">
                            <option value="">-{{trans('common.form.placeholder.select_first')}}-</option>
                            @foreach($roles as $k=>$role)
                                <option value="{{$role->name}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('admin.form.label.admin_permission')}}</label>
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
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
            @if(Auth::user()->hasRole('administrator'))
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="reset">{{trans('common.table.button.reset')}}</a>
            @endif
        </div>
    </script>

    <script>
        layui.config({
        base: "{{url('plugin/layui')}}/"
        }).extend({
        common: 'lay/modules/admin/common',
        formSelects: 'lay/modules/formSelects-v4',
        }).use(['common' , 'tree' , 'table' , 'formSelects'], function () {
            var $ = layui.jquery,
            table = layui.table,
            form = layui.form,
            tree = layui.tree,
            common = layui.common,
            formSelects = layui.formSelects
            formSelects.render('admin_roles');
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
                ,showCheckbox: true
                ,key: 'id'
                ,accordion: true
                ,id:'permission_tree'
            });
            table.init('table', {
                page:false
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'del'){
                    common.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/admin')}}/"+data.admin_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                } else if(layEvent === 'edit'){ //编辑
                    form.val("admin_form", {
                        "admin_id": data.admin_id // "name": "value"
                        ,"admin_username": data.admin_username
                        ,"admin_realname": data.admin_realname
                        ,"admin_email": data.admin_email
                        ,"admin_sex": data.admin_sex
                        ,"admin_country": data.admin_country
                        ,"admin_status": data.admin_status
                    });
                    tree.reload('permission_tree');
                    tree.setChecked('permission_tree', JSON.parse(data.admin_auth));
                    formSelects.value('admin_roles', JSON.parse(data.admin_roles));
                } else if(layEvent === 'reset'){ //编辑
                    common.confirm("{{trans('common.confirm.update')}}", function(index){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/admin')}}/"+data.admin_id+"/reset" , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'patch');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                }else if(layEvent === 'menu_toggle')
                {
                    treetable.toggleRows($(this).find('.treeTable-icon'), false);
                }
            });
            form.on('submit(form_submit_add)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined||k.startsWith('layuiTreeCheck'))
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
                        if(params.hasOwnProperty('admin_auth'))
                        {
                            params['admin_auth'][id] = auth;
                        }else{
                            params['admin_auth'] = {};
                            params['admin_auth'][id] = auth;
                        }
                    });
                });
                if(params.hasOwnProperty('admin_roles'))
                {
                    var admin_roles = params.admin_roles;
                    admin_roles = admin_roles.split(',');
                    params.admin_roles = admin_roles;
                }
                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/admin')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                        location.reload();
                    });
                });
                return false;
            });
            form.on('submit(form_submit_update)', function(data){
                var params = {};
                $.each(data.field , function (k ,v) {
                    if(v==''||v==undefined||k.startsWith('layuiTreeCheck'))
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
                        if(params.hasOwnProperty('admin_auth'))
                        {
                            params['admin_auth'][i] = auth;
                        }else{
                            params['admin_auth'] = {};
                            params['admin_auth'][i] = auth;
                        }
                        i++;
                    });
                });
                common.ajax("{{LaravelLocalization::localizeUrl('/backstage/admin/')}}/"+params.admin_id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
                return false;
            });
            form.on('submit(admin_form)', function(){
                return false;
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
