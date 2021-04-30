@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table img{ min-width:100px; max-width: 300px;}
        table div{width: 20%; float: left;}
        #layer-photos-demo {width: 100%}
        p {width: 60px;height: 20px; text-align: justify;float: left; font-weight: 600;}
        p > i {display: inline-block;padding-left: 100%;}
        .layui-table span {display: block;float: left;}
    </style>
    <div  class="layui-fluid">
        <table class="layui-table" lay-filter="post_table">
            <tr><th>
                    <div>
                        <form id="form-refuse" method="post">
                            {{ csrf_field() }}
                            <input name="status" hidden value="refuse">
                            <input name="post_id" hidden value="{{$result['post_id']}}">
                            <button class="layui-btn" style="background-color: #63BA79" type="submit" lay-submit="">不通过</button>
                        </form>
                    </div>
                    <div>
                        <form id="form-hot" method="post">
                            {{ csrf_field() }}
                            <input name="status" hidden value="pass">
                            <input name="post_id" hidden value="{{$result['post_id']}}">
                            <button class="layui-btn  layui-btn-warm" type="submit" lay-submit="">通过</button>
                        </form>
                    </div>
                    <div style="position: absolute;right: 15px; width: 220px; line-height: 40px;">
                            <span class="layui-btn layui-btn-normal" style="display: block; cursor:default">
                               <i style="color: black; font-weight: 600; font-size: 16px;margin-right: 20px;"> @if ($result['type']=='vote')  投票帖 @endif
                                   @if ($result['type']=='image') 图片帖 @endif
                                   @if ($result['type']=='video') 视频帖 @endif</i>
                                @if ($result['type']=='text') 文本帖 @endif</i>
                                待审核数量：<i style="font-weight: 600;font-size: 16px;">{{$result['unaudited']}}</i></span>
                    </div>
                </th>
            </tr>
            <tr style="display: none"><td>发布时间： {{date('Y-m-d H:i:s',strtotime($result['created_at'])+3600*8)}}</td></tr>
            <tr><td><span style="display: block; margin-right: 100px;"><b>UserName：</b>{{$result['owner']['user_nick_name']}}</span>
                    <span style="margin-right: 20px;">POST_ID：{{$result['post_id']}} </span>
                    <span style="height: 22px; line-height: 22px;">发布时间： {{date('Y-m-d H:i:s',strtotime($result['created_at'])+3600*8)}}</span>
                </td></tr>
            <tr><td><p>图片/视频<i></i></p><span>：</span>
                    <span class="layui-btn layui-btn-checked" style=" cursor:default; height: 20px;line-height: 20px;float: right;">
                            已领取: <i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$result['claim']}}</i>
                            今日已审核：<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$result['todayCount']}}</i>
                            累计已审核：<i style="font-weight: 600;font-size: 16px;">{{$result['totalCount']}}</i>
                            </span>
                </td></tr>
            <tr>
                <td>
                    @if ($result['type']=='image')
                        <div id="layer-photos-demo" class="layer-photos-demo">
                            <img layer-pid="{{$result['image']}}" alt="这里有图片☺" layer-src="{{$result['image']}}" src="{{$result['image']}}">
                        </div>
                    @else
                        <video controls="controls" autoplay="autoplay" poster="{{$result['image']}}"  width="100%" height="380px">
                            <source src="{{$result['video']}}" type="video/mp4" />
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </td>
            </tr>
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