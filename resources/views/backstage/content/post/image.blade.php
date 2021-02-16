@extends('layouts.app')
@section('content')
    <style>
        .layui-layout-body {overflow: auto;}
        .layui-table img{ min-width:100px; max-width: 300px;}
        table div{width: 30%; float: left;}
        #layer-photos-demo {width: 100%}
        p {width: 60px;height: 20px; text-align: justify;float: left; font-weight: 600;}
        p > i {display: inline-block;padding-left: 100%;}
        .layui-table span {display: block;float: left;}
        .layui-table td {padding: 5px 15px;}
    </style>
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="post_table">
            @if (empty($result))
                <tr><td style="text-align: center; height: 500px; font-size: 18px;"></td></tr>
            @else
                @foreach($result['translations'] as $key=>$value)
                    <tr><td><p>{{$value['post_locale']}}<i></i></p><span>：</span><span>{{$value['post_content']}}</span></td></tr>
                @endforeach
                <tr><td>发布时间： {{date('Y-m-d H:i:s',strtotime($result['post_created_at'])+3600*8)}}</td></tr>
                <tr><td><p>图片/视频<i></i></p><span>：@if (!empty($result['media']['image']))
                                <i style="font-size: 10px;color: blue">图片数量：</i><b style="color: red;">{{count($result['media']['image']['image_url'])}}</b>
                                @if (count($result['media']['image']['image_url'])>4)
                                    <i style="color: red; font-size: 14px;padding-left: 10px;font-weight: 600">图片数量较多，请注意查看</i>
                                @endif
                                <i style="color: red; font-size: 14px;padding-left: 10px;">点击图片可查看原图，左右方向键切换图片，滚轮放大缩小</i>
                            @endif</span>
                    </td></tr>
                <tr>
                    <td>
                        @if (!empty($result['media']))
                            @if (!empty($result['media']['image']))
                                <div id="layer-photos-demo" class="layer-photos-demo">
                                    @foreach($result['media']['image']['image_url'] as $k=>$v)
                                        <img layer-pid="{{$k}}" alt="这里有图片☺" layer-src="{{$v}}" src="
                                            @if (!empty($result['media']['image']['thumb_image_url'][$k]))
                                        {{$result['media']['image']['thumb_image_url'][$k]}}
                                        @else
                                        {{$v}}
                                        @endif
                                                ">
                                    @endforeach
                                </div>
                            @else
                                <video controls="controls" autoplay="autoplay"  width="100%" height="380px">
                                    <source src="{{$result['media']['video']['video_url']}}" type="video/mp4" />
                                    Your browser does not support the video tag.
                                </video>
                            @endif

                        @endif
                    </td>
                </tr>
            @endif
        </table>
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.use(['layer'], function () {
            let $ = layui.jquery,
                layer = layui.layer;
            layer.photos({
                photos: '#layer-photos-demo'
                //,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            });
            $(document).on("mousewheel DOMMouseScroll", ".layui-layer-phimg img", function (e) {
                console.log('123');
                var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) || // chrome & ie
                    (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1)); // firefox
                var imagep = $(".layui-layer-phimg").parent().parent();
                var image = $(".layui-layer-phimg").parent();
                var h = image.height();
                var w = image.width();
                if (delta > 0) {

                    h = h * 1.05;
                    w = w * 1.05;

                } else if (delta < 0) {
                    if (h > 100) {
                        h = h * 0.95;
                        w = w * 0.95;
                    }
                }
                imagep.css("top", (window.innerHeight - h) / 2);
                imagep.css("left", (window.innerWidth - w) / 2);
                image.height(h);
                image.width(w);
                imagep.height(h);
                imagep.width(w);
            });
        })

    </script>
@endsection
