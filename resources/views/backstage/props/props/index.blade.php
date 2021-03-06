@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.name')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="name" id="name" value="@if(!empty($name)){{$name}}@endif" />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.category')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="category">
                            <option value="">All</option>
                            @foreach($categories as $cate)
                                <option value="{{$cate->name}}" @if(isset($category) && $category==$cate->name) selected @endif>{{$cate->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.sort')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="sort">
                            <option value="">All</option>
                            <option value="desc" @if(isset($sort) && $sort=='desc') selected @endif>DESC</option>
                            <option value="asc" @if(isset($sort) && $sort=='asc') selected @endif>ASC</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('business.table.header.recommend')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="recommend">
                            <option value="">All</option>
                            <option value="1" @if(isset($recommend) && $recommend=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($recommend) && $recommend=='0') selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.table.header.hot')}}:</label>
                    <div class="layui-input-inline">
                        <select  name="hot">
                            <option value="">All</option>
                            <option value="1" @if(isset($hot) && $hot=='1') selected @endif>YES</option>
                            <option value="0" @if(isset($hot) && $hot=='0') selected @endif>NO</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    <button id="add" type="button" class="layui-btn layui-btn-normal">Add</button>
                </div>
            </div>
        </form>
        <table class="layui-table"   lay-filter="table" id="table" >
            <thead>
            <tr>
                <th lay-data="{field:'id', width:80 , fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th lay-data="{field:'name', minWidth:100,sort:true, fixed: 'left'}">{{trans('common.table.header.name')}}</th>
                <th lay-data="{field:'remark', width:200,edit: 'text'}">{{trans('common.table.header.remark')}}</th>
                <th lay-data="{field:'cover', width:80}">{{trans('user.table.header.user_avatar')}}</th>
                <th lay-data="{field:'recommendation', width:120}">{{trans('business.table.header.recommend')}}</th>
                <th lay-data="{field:'category', minWidth:120}">{{trans('common.table.header.category')}}</th>
                <th lay-data="{field:'camera', width:80}">Camera</th>
                <th lay-data="{field:'hot', width:100}">{{trans('common.table.header.hot')}}</th>
                <th lay-data="{field:'sort', width:100,edit: 'text',sort:true}">{{trans('common.form.label.sort')}}</th>
                <th lay-data="{field:'is_delete', width:100}">{{trans('common.table.header.status')}}</th>
                <th lay-data="{field:'url', minWidth:360}">URL</th>
                <th lay-data="{field:'hash', width:280}">Hash(MD5)</th>
                <th lay-data="{field:'created_at', width:160}">{{trans('common.table.header.created_at')}}</th>
                <th lay-data="{fixed: 'right', minWidth:100, align:'center', toolbar: '#op'}">{{trans('common.table.header.op')}}</th>

            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->remark}}</td>
                    <td><img style="width: 33px;" src="{{$value->cover}}"></td>
                    <td><input type="checkbox" @if($value->recommendation==1) checked @endif name="recommendation" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->category}}</td>
                    <td>{{$value->camera}}</td>
                    <td><input type="checkbox" @if($value->hot==1) checked @endif name="hot" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
                    <td>{{$value->sort}}</td>

                    <td><input type="checkbox" @if($value->is_delete==0) checked @endif name="is_delete" lay-skin="switch" lay-filter="switchAll" lay-text="ON|OFF"></td>
                    <td>{{$value->url}}</td>
                    <td>{{$value->hash}}</td>
                    <td>{{$value->created_at}}</td>
{{--                    <td>{{$value->updated_at}}</td>--}}
{{--                    <td>@if($value->deleted_at=='0000-00-00 00:00:00')@else{{$value->deleted_at}}@endif</td>--}}
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $data->links('vendor.pagination.default') }}
        @else
            {{ $data->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //??????????????????
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table,
                common = layui.common;
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                var data = obj.data; //?????????????????????
                var layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                var tr = obj.tr; //??????????????? tr ???DOM??????
               if(layEvent === 'edit'){ //??????
                    var id = data.id;
                    layer.open({
                        type: 2,
                        title: 'Props settings',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['360px', '100%'],
                        content: '/backstage/props/props/'+id+'/edit',
                    });
                }
            });

            //?????????????????????
            table.on('edit(table)', function(obj){
                var that = this;
                var value = obj.value //?????????????????????
                    ,data = obj.data //???????????????????????????
                    ,field = obj.field //????????????
                    ,original = $(this).prev().text(); //????????????
                var params = d = {};
                d[field] = original;
                @if(!Auth::user()->can('props::props.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                obj.update(d);
                $(this).val(original);
                table.render();
                return true;
                @endif
                    params[field] = value;
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/props/props')}}/"+data.id , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        table.render();
                    } , 'PATCH' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            obj.update(d);
                            $(that).val(original);
                            table.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]} , function(){
                    d[field] = original;
                    obj.update(d);
                    $(that).val(original);
                    table.render();
                });
            });

            form.on('switch(switchAll)', function(data){
                var checked = data.elem.checked;
                var propsId = data.othis.parents('tr').find("td :first").text();
                data.elem.checked = !checked;
                @if(!Auth::user()->can('props::props.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif;
                var name = $(data.elem).attr('name');
                if(checked) {
                    var params = '{"'+name+'":"on"}';
                }else {
                    var params = '{"'+name+'":"off"}';
                }
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax("{{url('/backstage/props/props')}}/"+propsId , JSON.parse(params) , function(res){
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
            $(document).on('click','#add',function(){
                layer.open({
                    type: 2,
                    title: 'Props settings',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['360px', '90%'],
                    offset: 'auto',
                    scrollbar: true,
                    content: '/backstage/props/props/create',
                });
            });
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
    </script>
@endsection
