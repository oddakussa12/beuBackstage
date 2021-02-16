@extends('layouts.app')
@section('content')
    @php
        $qn_token = qnToken('qn_image_sia');
    @endphp
    <div  class="layui-fluid" id="layui-fluid" style="text-align: center;">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>banner图</legend>
        </fieldset>

        <ul class="layui-tab-title">
            @foreach($supportLanguage as $language=>$word)
                <li class="layui-btn top_image" id="{{$language}}">{{$word}}@if(!isset($images[$language])) <span style="color: red">无</span> @endif</li>
            @endforeach
        </ul>

        <div class="layui-carousel" id="top_carousel"  style="margin:0 auto;" >
            <div carousel-item="" id="carousel_item">
                @foreach($supportLanguage as $language=>$word)
                    @if(isset($images[$language]))
                        <div class="img-item item_{{$language}}">{{$language}}
                            <img src="{{config('common.qnUploadDomain.thumbnail_domain')}}/{{$images[$language]}}?imageView2/0/w/1200/h/1200/interlace/1|imageslim">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <style>
        .img-item img{width:auto;height:auto; max-width:100%; max-height:100%;}
    </style>

@endsection
@section('footerScripts')
    @parent
    <script>
        //内容修改弹窗
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar'
        }).use(['common' , 'table' , 'layer' , 'carousel' , 'element' , 'upload' , 'loadBar'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                loadBar = layui.loadBar,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery,
                upload = layui.upload,
                element = layui.element;
                carousel.render({
                    elem: '#top_carousel'
                    ,width: '1200px'
                    ,height: '600px'
                    ,interval: 1000
                });

            // //监听折叠
            // element.on('collapse(image)', function(data){
            //     // layer.msg('展开状态：'+ data.show);
            // });
            $('.top_image').each(function(){
                var btn = $(this);
                upload.render({
                    elem: btn //绑定元素
                    , url: 'https://upload-as0.qiniup.com/' //上传接口
                    , method: 'post'
                    , data: {
                        //key: 'aaa.png',  //自定义文件名
                        token: "{{$qn_token['token']}}"
                    }
                    , before: function (obj) {
                        loadBar.start();
                        //预读本地文件示例，不支持ie8
                        // obj.preview(function (index, file, result) {
                        //     $('#demo1').attr('src', result); //图片链接（base64）
                        // });
                    }
                    , done: function (res, index, upload) {

                        var id = "{{$id}}";
                        var param = {};
                        param.locale = btn.attr('id');
                        param.image = res.name;
                        common.ajax("{{ url('/backstage/content/banner') }}/"+id,param,function(res){
                            loadBar.finish();
                           // window.location.reload();
                        } , 'put' , function (e,xhr,opt) {
                            var msg = '未知错误';
                            if((opt=='Unprocessable Entity'&&e.status==422)||e.status==423)
                            {
                                var res = e.responseJSON;
                                msg = res.errors.email[0];
                            }else if(e.status==302)
                            {
                                var res = e.responseJSON;
                                msg = res.message;
                            }
                            common.prompt(msg , 5 , 1000 , 6 , 'auto' ,function () {
                                loadBar.error();
                            });
                        });
                    }
                    , error: function () {
                        //演示失败状态，并实现重传
                        return layer.msg('error');
                    }

                });
            });
        });
    </script>

@endsection