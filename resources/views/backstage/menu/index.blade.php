@extends('layouts.dashboard')

@section('layui-content')
    @php
        $supportedLocales = LaravelLocalization::getSupportedLocales();
    @endphp
    <div class="layui-container">
        <table class="layui-table treeTable" lay-filter="tree_menu">
            <thead>
            <tr>
                <th lay-data="{field:'menu_id', width:80}">{{trans('menu.table.header.menu_id')}}</th>
                <th lay-data="{field:'menu_p_id', hide:true}">{{trans('menu.table.header.menu_p_id')}}</th>
                <th lay-data="{field:'menu_p_name', minWidth:200 , event:'menu_toggle'}">{{trans('menu.table.header.menu_name')}}</th>
                @if(count($supportedLocales)>=1)
                    @foreach($supportedLocales as $localeCode => $properties)
                        <th lay-data="{field:'{{$localeCode}}_menu_name', maxWidth:80}">{!! $properties['native'] !!}</th>
                    @endforeach
                @endif
                <th lay-data="{field:'menu_auth', minWidth:200}">{{trans('menu.table.header.menu_auth')}}</th>
                <th lay-data="{field:'menu_url', width:250}">{{trans('menu.table.header.menu_url')}}</th>
                <th lay-data="{field:'menu_op', minWidth:120 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($all_menu as $k=>$v)
                <tr>
                    <td>{{$v->menu_id}}</td>
                    <td>{{$v->menu_p_id}}</td>
                    <td>
                    <span class="treeTable-icon open" lay-tid="{{$v->menu_id}}" lay-tpid="{{$v->menu_p_id}}" @if($v->child) lay-ttype="dir" @else lay-ttype="file" @endif>
                        @for($i=0;$i<=$v->menu_level;$i++)
                            <span class="treeTable-empty"></span>
                        @endfor
                        @if($v->child)
                            <i class="layui-icon layui-icon-triangle-d"></i>
                            <i class="layui-icon layui-icon-layer"></i>
                        @else
                            <i class="layui-icon layui-icon-file"></i>
                        @endif
                        {{$v->menu_name}}
                    </span>
                    </td>
                    @if(count($supportedLocales)>=1)
                        @foreach($supportedLocales as $localeCode => $properties)
                            @php
                                $flag = false;
                            @endphp
                            @foreach($v->translations as $translation)
                                @if($translation->menu_locale==$localeCode)
                                    @php
                                        $flag = true;
                                    @endphp
                                    <td>{{$translation->menu_name}}</td>
                                @endif
                            @endforeach
                            @if($flag===false)
                                <td></td>
                            @endif
                        @endforeach
                    @endif
                    <td>{{$v->menu_auth}}</td>
                    <td>{{$v->menu_url}}</td>
                    <td></td>
                </tr>
                @isset($v->child)
                    @include('backstage.menu.child_menu' , ['child_menu'=>$v->child , 'supportedLocales'=>$supportedLocales])
                @endisset

            @endforeach

            </tbody>
        </table>

        <div class="layui-tab layui-tab-card">
            <ul class="layui-tab-title">
                @if(count($supportedLocales)>=1)
                    @foreach($supportedLocales as $localeCode => $properties)
                        <li class="{{ App::getLocale() == $localeCode ? 'layui-this' : '' }}">
                            {!! $properties['native'] !!}
                        </li>
                    @endforeach
                @endif
            </ul>

            {{--<div class="layui-tab-content" >--}}

            <form class="layui-form layui-tab-content"  lay-filter="menu_form">
                                        <div class="layui-form-item">
                                            <div class="layui-inline">
                                                <div class="layui-input-block">
                                                    <input type="hidden" name="menu_id" />
                                                </div>
                                            </div>
                                        </div>

                @if(count($supportedLocales)>=1)
                    @foreach($supportedLocales as $localeCode => $properties)
                        <div class="layui-tab-item {{ App::getLocale() == $localeCode ? 'layui-show' : '' }}">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">{{trans('menu.form.form_menu_name')}}</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="{{$localeCode}}_menu_name"   lay-verify="menu_name" placeholder="{{trans('menu.placeholder.menu_name')}}" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('menu.form.form_menu_auth')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="menu_auth"  placeholder="{{trans('menu.placeholder.menu_auth')}}" lay-verify="menu_auth" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('menu.form.form_menu_url')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="menu_url"  placeholder="{{trans('menu.placeholder.menu_url')}}" lay-verify="menu_url" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('menu.form.form_menu_f_id')}}</label>
                        <div class="layui-input-block">
                            <select name="menu_p_id" >
                                <option value="0">-{{trans('common.form.placeholder.select_first')}}-</option>
                                @foreach($all_menu as $menu)
                                    <option id="menu_class_id_{{$menu->menu_id}}" value="{{$menu->menu_id}}" layui-level="{{$menu->menu_level}}" layui-path="{{$menu->menu_path}}">{{$menu->menu_format_name}}</option>
                                    @isset($menu->child)
                                        @include('backstage.menu.child_menu_name' , ['child_menu'=>$menu->child]);
                                    @endisset
                                @endforeach
                            </select>
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
    <link id="layuicss-http:backstagemantoucompluginlayuistyletreetablecss" rel="stylesheet" href="/plugin/layui/style/treetable.css" media="all" />
    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            treetable: 'lay/modules/treetable',
            common: 'lay/modules/admin/common',
        }).use(['treetable' , 'common'], function () {
            var $ = layui.jquery;
            var table = layui.table;
            var form = layui.form;
            var treetable = layui.treetable;
            var common = layui.common;
            var supportedLocales = @json($supportedLocales);
            table.init('tree_menu', {
                page:false,
                limit:100000
            });
            treetable.init_load();
            table.on('tool(tree_menu)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'del'){
                    common.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/menu')}}/"+data.menu_id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                location.reload();
                            });
                        } , 'delete');
                    } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
                } else if(layEvent === 'edit'){ //编辑
                    form.val("menu_form", {
                        "menu_id": data.menu_id // "name": "value"
                        ,"menu_name": data.menu_name
                        @if(count($supportedLocales)>=1)
                        @foreach($supportedLocales as $localeCode => $properties)
                        ,"{{$localeCode}}_menu_name": data["{{$localeCode}}_menu_name"]
                        @endforeach
                        @endif
                        ,"menu_auth": data.menu_auth
                        ,"menu_url": data.menu_url
                        ,"menu_p_id": data.menu_p_id
                    });
                }else if(layEvent === 'menu_toggle')
                {
                    treetable.toggleRows($(this).find('.treeTable-icon'), false);
                }
            });
            form.verify({
                menu_name:function(value, item){
                    if(value==''||value==undefined)
                    {
                        return "{{trans('menu.prompt.menu_name_required')}}";
                    }
                },
                menu_auth:function(value, item){
                },
                menu_url:function(value, item){
                }
            });
            form.on('submit(form_submit_add)', function(data){
                var params = common.fieldToArr(data.field , supportedLocales);
                common.ajax("{{url('/backstage/menu')}}" , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.add')}}"  , 1 , 1500 , 6 , 't' ,function () {
                    });
                });
                return false;
            });
            form.on('submit(form_submit_update)', function(data){
                var current_menu_id = data.field.menu_id;
                if(current_menu_id==''||current_menu_id==undefined||current_menu_id==0)
                {
                    common.prompt("{{trans('menu.prompt.menu_id_required')}}" , 5 , 1000 , 1 , 't');return false;
                }
                var value = $(data.form).find("dl dd[class=layui-this]").attr('lay-value');
                if(value!=0)
                {
                    var path = $(data.form).find("select[name=menu_p_id] option[value="+value+"]").attr('layui-path');
                    path = path.split('_');
                    if($.inArray(current_menu_id , path)!=-1 && $.inArray(current_menu_id , path)<=$.inArray(value , path))
                    {
                        common.prompt("{{trans('menu.prompt.menu_level_little')}}" , 5 , 1000 , 1 , 't');return false;
                    }
                }
                var params = common.fieldToArr(data.field , supportedLocales);
                common.ajax("{{url('/backstage/menu/')}}/"+current_menu_id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'put');
                return false;
            });
            form.on('submit(menu_form)', function(){
                return false;
            });
        });
    </script>
@endsection
