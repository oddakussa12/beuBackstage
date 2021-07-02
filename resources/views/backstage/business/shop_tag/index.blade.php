@extends('layouts.dashboard')
@section('layui-content')
    @php
        $supportedLocales = LaravelLocalization::getSupportedLocales();
    @endphp
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', minWidth:100}">{{trans('business.table.header.shop_tag.id')}}</th>
                <th  lay-data="{field:'tag', width:120}">{{trans('business.table.header.shop_tag.tag')}}</th>
                <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                <th  lay-data="{field:'{{ $locale }}_tag_content', width:300}">{{ $locale }}</th>
                <?php endforeach; ?>
                <th  lay-data="{field:'translation_op', minWidth:120 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shopTags as $key=>$shopTag)
                <tr>
                    <td>{{ $shopTag->id }}</td>
                    <td>{{ $shopTag->tag }}</td>
                    <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                    <td>{{ is_array(array_get($shopTag, $locale, null)) ?: array_get($shopTag, $locale, '') }}</td>
                    <?php endforeach; ?>
                    <td></td>
                </tr>
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
            <form class="layui-form layui-tab-content"  lay-filter="tag_form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-block">
                            <input type="hidden" name="id" />
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{{trans('business.form.label.shop_tag.tag')}}</label>
                        <div class="layui-input-block">
                            <input type="text" name="tag"  placeholder="{{trans('business.form.placeholder.shop_tag.tag')}}" lay-verify="tag" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
                @if(count($supportedLocales)>=1)
                    @foreach($supportedLocales as $localeCode => $properties)
                        <div class="layui-tab-item {{ App::getLocale() == $localeCode ? 'layui-show' : '' }}">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">{{trans('business.form.label.shop_tag.tag_content')}}</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="{{$localeCode}}_tag_content"   lay-verify="tag_content" placeholder="{{trans('business.form.placeholder.shop_tag.tag_content')}}" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
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
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
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
                common = layui.common,
                layer = layui.layer;




            table.init('table', { //转化静态表格
                page:true
            });

            table.on('tool(table)', function(obj){
                var data = obj.data;
                console.log(data);
                var layEvent = obj.event;
                var tr = obj.tr;
                console.log(tr);
                if(layEvent === 'edit'){ //编辑
                    form.val("tag_form", {
                        "id": data.id,
                        "tag": data.tag
                        @if(count($supportedLocales)>=1)
                        @foreach($supportedLocales as $localeCode => $properties)
                        ,"{{$localeCode}}_tag_content": data["{{$localeCode}}_tag_content"]
                        @endforeach
                        @endif
                    });
                    console.log(form);
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

                common.ajax("{{url('/backstage/business/shop_tag')}}" , params , function(res){
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

                common.ajax("{{url('/backstage/business/shop_tag')}}/"+params.id , params , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't' ,function () {
                        location.reload();
                    });
                } , 'patch');
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
