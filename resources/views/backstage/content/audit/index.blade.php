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
            @if (empty($result))
                <tr><td style="text-align: center; height: 500px; font-size: 18px;">真厉害，都审核完成了呢！休息一下吧☺☺</td></tr>
            @else
                <tr><th>
                        <div>
                            <form id="form-refuse" method="post">
                                {{ csrf_field() }}
                                <input name="status" hidden value="refuse">
                                <input name="uuid" hidden value="{{$result['post_uuid']}}">
                                <input name="id" hidden value="{{$result['post_id']}}">
                                <button class="layui-btn" style="background-color: #63BA79" type="submit" lay-submit="">不通过</button>
                            </form>
                        </div>
                        <div>
                            <form id="form-hot" method="post">
                                {{ csrf_field() }}
                                <input name="status" hidden value="hot">
                                <input name="uuid" hidden value="{{$result['post_uuid']}}">
                                <input name="id" hidden value="{{$result['post_id']}}">
                                <button class="layui-btn  layui-btn-warm" type="submit" lay-submit="">通过并上热门</button>
                            </form>
                        </div>
                        <div>
                            <form id="form-hot" method="post">
                                {{ csrf_field() }}
                                <input name="status" hidden value="preheat">
                                <input name="uuid" hidden value="{{$result['post_uuid']}}">
                                <input name="id" hidden value="{{$result['post_id']}}">
                                <button class="layui-btn layui-btn-danger" type="submit" lay-submit="">通过并上热门预热</button>
                            </form>
                        </div>
                        <div>
                            <form id="form-pass" method="post">
                                {{ csrf_field() }}
                                <input name="status" hidden value="pass">
                                <input name="uuid" hidden value="{{$result['post_uuid']}}">
                                <input name="post_hotting" hidden value="off">
                                <input name="id" hidden value="{{$result['post_id']}}">
                                <button class="layui-btn" type="submit" lay-tips="通过不上热门" lay-submit="">通过不上热门</button>
                            </form>
                        </div>
                        <div style="position: absolute;margin-left: 80%; line-height: 40px;">
                            <span class="layui-btn layui-btn-normal" style="display: block; cursor:default">
                               <i style="color: black; font-weight: 600; font-size: 16px;margin-right: 20px;"> @if ($result['post_type']=='vote')  投票帖 @endif
                                @if ($result['post_type']=='image') 图片帖 @endif
                                @if ($result['post_type']=='video') 视频帖 @endif</i>
                                @if ($result['post_type']=='text') 文本帖 @endif</i>
                                待审核数量：<i style="font-weight: 600;font-size: 16px;">{{$result['unaudited']}}</i></span>
                        </div>
                    </th>
                </tr>

                <tr><td><p>原始语言<i></i></p><span>：</span><span>{{$result['post_default_content']}}</span></td></tr>
                @if (!empty($result['trans']))
                    <tr><td><p>中文<i></i></p><span>：</span>@if (!empty($result['trans']['zh-CN']))<span>{{$result['trans']['zh-CN']}}</span>@endif</td></tr>
                    <tr><td><p>英文<i></i></p><span>：</span>@if (!empty($result['trans']['en']))<span>{{$result['trans']['en']}}</span>@endif</td></tr>
                @endif
            @if (!empty($result['vote_info']))
                    <tr>
                        <td><p style="color: red">投票选项<i></i></p><span>：</span>
                            @foreach($result['vote_info'] as $key=>$val)
                                @if (!empty($val->tab_name))
                                <div style=" width:270px; margin-right: 15px;">
                                    <span>选项{{$val->tab_name}}：</span>
                                    @if (!empty($val->vote_media))
                                        <img style="width: 270px" src="{{$val->vote_media['image']['image_url']}}"/>
                                    @endif
                                    <span>{{$val->content}}</span>
                                </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @endif
            <tr style="display: none"><td>发布时间： {{date('Y-m-d H:i:s',strtotime($result['post_created_at'])+3600*8)}}</td></tr>
                <tr><td><span style="display: block; margin-right: 100px;"><b>USER_ID：</b>{{$result['user_id']}}
                            <a style="color: blue" target="_blank" href="{{route('passport::user.index')}}?field=user_id&value={{$result['user_id']}}">用户页</a>
                            <a style="color: red" target="_blank" href="{{route('content::post.index')}}?user_id={{$result['user_id']}}">帖子页</a></span>
                        <span style="margin-right: 20px;">POST_ID：{{$result['post_id']}} </span>
                        <span> POST_UUID：
                            <a target="_blank" style="color: #63BA79" href="{{route('content::post.index')}}?field=post_uuid&v={{$result['post_uuid']}}">{{$result['post_uuid']}}</a>
                        </span>
                        <span style="margin-left: 7%">
                            <span class="layui-btn layui-btn-normal" style="height: 22px; line-height: 22px;">@if(!empty($result['post_deleted_at']))已删除@else未删除@endif</span>
                            发布时间： {{date('Y-m-d H:i:s',strtotime($result['post_created_at'])+3600*8)}}</span>
                    </td></tr>
                <tr><td><p>图片/视频<i></i></p><span>：@if (!empty($result['media']['image']))
                                <i style="font-size: 10px;color: blue">图片数量：</i><b style="color: red;">{{count($result['media']['image']['image_url'])}}</b>
                                @if (count($result['media']['image']['image_url'])>4)
                                    <i style="color: red; font-size: 14px;padding-left: 10px;font-weight: 600">图片数量较多，请注意查看</i>
                                @endif
                                <i style="color: red; font-size: 14px;padding-left: 10px;">点击图片可查看原图，左右方向键切换图片，滚轮放大缩小 </i>
                @endif
                        </span>
                        <span class="layui-btn layui-btn-checked" style=" cursor:default; height: 20px;line-height: 20px;float: right; margin-right:20px;">
                             <i style="font-size: 12px; padding-right: 50px;">
                                 点赞数：<i style="font-size: 14px; font-weight: 600; color: red">{{$result['post_like_num']}} </i>
                                 评论数：<i style="font-size: 14px; font-weight: 600; color: red">{{$result['post_comment_num']}}</i></i>
                            今日已审核：<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$result['todayCount']}}</i>
                            累计已审核：<i style="font-weight: 600;font-size: 16px;">{{$result['totalCount']}}</i>
                            </span>
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
