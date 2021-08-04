@extends('layouts.dashboard')

@section('layui-content')
    @php
        $supportedLocales = LaravelLocalization::getSupportedLocales();
    @endphp
    <div style="padding: 20px;">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-tab" lay-filter="modules">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="general">{{trans('config.tab.header.general')}}</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div style="padding: 20px; background-color: #F2F2F2;">
                                <div class="layui-row layui-col-space15">
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">{{trans('config.tab.title.special_goods')}}</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <div class="layui-form-item">
                                                        <div class="layui-inline">
                                                            <div class="layui-upload" style="margin-left: 110px;">
                                                                <button type="button" class="layui-btn" id="image"><i class="layui-icon"></i></button>
                                                                <input type="hidden" id="hide_image" name="set[general][special_goods][banner_image]" />
                                                                <input type="hidden"  name="remote" value='{"url":"api/backstage/special_goods/image","method":"patch","params":{"image":"{{config('set.general.special_goods.banner_image')}}"}}' />
                                                                <div class="layui-upload-list">
                                                                    <img class="layui-upload-img" id="show_image" width="90px" height="90px" src="{{config('set.general.special_goods.banner_image')}}">
                                                                </div>
                                                                <div style="width: 95px;">
                                                                    <div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="image_progress">
                                                                        <div class="layui-progress-bar" lay-percent=""></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div class="layui-card">
                                            <div class="layui-card-header">{{trans('config.tab.title.discounted_price')}}</div>
                                            <div class="layui-card-body">
                                                <form class="layui-form"  lay-filter="config_form">
                                                    <div class="layui-form-item">
                                                        <div class="layui-inline">
                                                            <label class="layui-form-label">{{trans('config.tab.title.discounted_price')}}</label>
                                                            <input type="hidden" name="set[general][promo_official_price]" value="0">
                                                            <input type="hidden"  name="remote" value='{"url":"api/backstage/goods_discounted/switch","method":"patch","params":{"switch":"{{config('set.general.promo_official_price')}}"}}' />
                                                            <div class="layui-input-block">
                                                                <input type="checkbox" name="set[general][promo_official_price]" value="1" lay-skin="switch" @if(config('set.general.promo_official_price')==1) checked @endif  lay-filter="switchAll"  />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-item">
                                                        <div class="layui-input-block">
                                                            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="config_submit_btn">{{trans('config.common.button.save')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        }).use(['form' , 'element' , 'common' , 'upload'], function() {
            var $ = layui.jquery
                , element = layui.element
                , upload = layui.upload
                ,form = layui.form
                ,common = layui.common;
            //Hash地址的定位
            var layid = location.hash.replace(/^#modules=/, '');
            element.on('tab(modules)', function (elem) {
                location.hash = 'modules=' + $(this).attr('lay-id');
            });
            form.on('submit(config_form)', function(data){
                return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
            });
            form.on('submit(config_submit_btn)', function(data){
                @if(!Auth::user()->can('config.store'))
                data.form.reset();
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", data.elem);
                form.render();
                return false;
                @endif
                common.confirm("{{trans('common.confirm.update')}}" , function(){
                        common.ajax("{{LaravelLocalization::localizeUrl('/backstage/config/')}}" , data.field , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                        } , 'post');
                } , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
            });

            form.on('switch(switchAll)', function(data){
                var val = $.parseJSON($(data.elem).parent().siblings('input[name=remote]').val());
                val.params.switch = data.elem.checked?1:0;
                $(data.elem).parent().siblings('input[name=remote]').attr('value' , JSON.stringify(val));
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
                ,choose:function (obj){
                    var files = obj.pushFile();
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
                                var ob = $('#image').siblings('input[name=remote]');
                                var val = $.parseJSON(ob.val());
                                val.params.image = res.domain+res.form.key;
                                ob.attr('value' , JSON.stringify(val));
                                $('#hide_image').attr('value' , res.domain+res.form.key);
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
        });
    </script>
@endsection