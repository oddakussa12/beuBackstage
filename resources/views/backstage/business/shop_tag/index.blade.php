@extends('layouts.dashboard')
@section('layui-content')
    @php
        $supportedLocales = LaravelLocalization::getSupportedLocales();
    @endphp
    <div  class="layui-container">
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th lay-data="{field:'id', minWidth:300}">{{trans('business.table.header.shop_tag.id')}}</th>
                <th  lay-data="{field:'tag', width:120}">{{trans('business.table.header.shop_tag.tag')}}</th>
                <th  lay-data="{field:'image', width:120,hide:true}">{{trans('business.table.header.shop_tag.image')}}</th>
                <th  lay-data="{field:'status', width:100}">{{trans('business.table.header.shop_tag.status')}}</th>
                <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                <th  lay-data="{field:'{{ $locale }}_tag_content', width:200}">{{ $locale }}</th>
                <?php endforeach; ?>
                <th  lay-data="{field:'translation_op', maxWidth:80 , templet: '#operateTpl'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shopTags as $key=>$shopTag)
                <tr>
                    <td>{{ $shopTag->id }}</td>
                    <td>{{ $shopTag->tag }}</td>
                    <td>{{ $shopTag->image }}</td>
                    <td><input type="checkbox" @if($shopTag->status==1) checked @endif name="status" lay-skin="switch" lay-filter="switchAll" lay-text="YES|NO"></td>
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
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-upload" style="margin-left: 110px;">
                            <button type="button" class="layui-btn" id="image"><i class="layui-icon"></i></button>
                            <input type="hidden" name="image" />
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="show_image" width="90px" height="90px">
                            </div>
                            <div style="width: 95px;">
                                <div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="image_progress">
                                    <div class="layui-progress-bar" lay-percent=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a name="list-progress"> </a>
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
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer' , 'upload' , 'element'], function () {
            var $ = layui.jquery,
                element = layui.element,
                upload = layui.upload,
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
                    if(data.image!=''&&data.image!=undefined)
                    {
                        $('#show_image').attr('src', data.image);
                    }else{
                        $('#show_image').attr('src' , 'https://imgservices-1252317822.image.myqcloud.com/image/20201015/45prvdakqe.svg');
                    }
                    form.val("tag_form", {
                        "id": data.id,
                        "tag": data.tag,
                        "image": data.image
                        @if(count($supportedLocales)>=1)
                        @foreach($supportedLocales as $localeCode => $properties)
                        ,"{{$localeCode}}_tag_content": data["{{$localeCode}}_tag_content"]
                        @endforeach
                        @endif
                    });
                    console.log(form);
                }
                form.render();
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
            var jqureAjaxXhrOnProgress = function(fun) {
                jqureAjaxXhrOnProgress.onprogress = fun; //绑定监听
                //使用闭包实现监听绑
                return function() {
                    //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
                    var xhr = $.ajaxSettings.xhr();
                    //判断监听函数是否为函数
                    if (typeof jqureAjaxXhrOnProgress.onprogress !== 'function')
                        return xhr;
                    //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
                    if (jqureAjaxXhrOnProgress.onprogress && xhr.upload) {
                        xhr.upload.onprogress = jqureAjaxXhrOnProgress.onprogress;
                    }
                    return xhr;
                }
            }

            var image = upload.render({
                elem: '#image'
                ,accept: 'images' //视频
                ,auto: false
                ,choose:function (obj , test){
                    var formData = new FormData();
                    var files = obj.pushFile();
                    // console.log(files);
                    var keys = Object.keys(files);
                    var end = keys[keys.length-1]
                    var file = files[end];
                    var formData = new FormData();

                    common.ajax("{{config('common.lovbee_domain')}}api/aws/image/form?file="+file.name , {} , function(res){
                        image.config.url = res.action;
                        for (p in res.form) {
                            formData.append(p, res.form[p]);
                        }
                        formData.append('file',file);
                        $.ajax({
                            url:res.action,
                            type:'POST',
                            data: formData,
                            processData:false,
                            cache:false,
                            contentType:false,
                            xhr:jqureAjaxXhrOnProgress(function(e){
                                var percent=e.loaded / e.total;
                                percent = Math.round((percent + Number.EPSILON) * 100);
                                element.progress('image_progress', percent+'%'); //进度条复位
                            }),
                            beforeSend: function(obj){
                                element.progress('image_progress', '0%');
                            },
                            success:function(data){
                                $('input[name=image]').attr('value' , res.domain+res.form.key);
                                $('#show_image').attr('src', res.domain+res.form.key); //图片链接（base64）
                            },
                            error:function(){
                                alert('upload failed');
                            },
                            complete:function (){
                                layer.closeAll();
                            }
                        })
                    } , 'get' , undefined , undefined , false);
                }
            });
            form.on('switch(switchAll)', function(data){
                let checked = data.elem.checked;
                data.elem.checked = !checked;
                const name = $(data.elem).attr('name');
                let id = data.othis.parents('tr').find("td :first").text();
                const url = "{{url('/backstage/business/shop_tag')}}/"+id;
                var params = {};
                if(checked) {
                    params[name] = "on";
                }else {
                    params[name] = "off";
                }
                @if(!Auth::user()->can('business::shop_tag.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.othis);
                form.render();
                return false;
                @endif
                form.render();
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                    common.ajax(url , params , function(res){
                        data.elem.checked = checked;
                        form.render();
                        common.prompt(res.result , 1 , 300 , 6 , 't');
                    } , 'patch' , function (event,xhr,options,exc) {
                        setTimeout(function(){
                            common.init_error(event,xhr,options,exc);
                            data.elem.checked = !checked;
                            form.render();
                        },100);
                    });
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
