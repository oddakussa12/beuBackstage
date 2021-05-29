@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .layui-table img{ min-width:100px; max-width: 300px;}
        table div{ margin: 0 5px; float: left;}
        .layui-btn {border-radius:6px; padding: 0 12px}
        .layui-table td, .layui-table th { padding: 12px 15px}
        .margin-left { margin-left: 5%;}
        @media screen and (min-width:768px) {
            table div{width: 20%;}
            .layui-btn { padding: 0 18px}
            .margin-left { margin-left: 30%;}
        }
        table td span {word-break: break-all;}
        .layer-photos-demo {width: 100%}
        p {width: 60px;height: 20px; text-align: justify;float: left; font-weight: 600;}
        p > i {display: inline-block;padding-left: 100%;}
        .layui-table span {display: block;float: left;}
    </style>
    <div class="layui-fluid">
        <table class="layui-table" lay-filter="post_table">
            @if (empty($result['comment_id']))
                @if(empty($result['unaudited']))
                <tr><td colspan="2"><h5>Amazing! The audit is all done! Take a break ☺☺</h5><br></td></tr>
                @else
                    <tr><td colspan="2">
                            <h5>To Be Reviewed:<i style="font-weight: 600;font-size: 16px;">{{$result['unaudited']}}</i></h5><br>
                            <form method="get" action="{{route('business::review.claim')}}">
                                <button class="layui-btn layui-btn-normal" type="submit">Continue to review</button>
                            </form>
                        </td></tr>
                @endif
                    <tr><td>Reviewed Today:</td><td><i style="font-weight: 600;font-size: 16px;">{{$result['todayCount']}}</i></td></tr>
                    <tr><td>Total:</td><td><i style="font-weight: 600;font-size: 16px;">{{$result['totalCount']}}</i></td></tr>
            @else
                <tr><td>ShopName:</td><td><span>{{$result['shop_nick_name']}}</span></td></tr>
                <tr><td>GoodsName:</td><td><span>{{$result['goods_name']}}</span></td></tr>
                <tr><td>GoodsImage:</td><td><span>
                            <div class="layer-photos-demo">
                                @if(!empty($result['image']))
                                @foreach($result['image'] as $image)
                                    <img style="min-width: 30px; max-width: 40px;" src="{{$image['url']}}">
                                @endforeach
                                @endif
                            </div>
                        </span>
                    </td></tr>
                <tr><td>Point:</td><td><span>{{$result['point']}}</span></td></tr>
                <tr><td>Service:</td><td><span>{{$result['service']}}</span></td></tr>
                <tr><td>Quality:</td><td><span>{{$result['quality']}}</span></td></tr>
                <tr><td>Content:</td><td><span>{{$result['content']}}</span></td></tr>
                <tr><td colspan="2">
                        @if (!empty($result['media']))
                            <div class="layer-photos-demo">
                                @foreach($result['media'] as $media)
                                    @if(stripos($media['url'], '.mp4')!==false)
                                        <video controls="controls" autoplay="autoplay" poster="{{$result['image']}}"  width="100%" height="380px">
                                            <source src="{{$media['url']}}" type="video/mp4" />
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img layer-pid="{{$media['url']}}" layer-src="{{$media['url']}}" src="{{$media['url']}}">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
                <tr><td>Submitted Time:</td>
                    <td><span>{{date('Y-m-d H:i:s',strtotime($result['created_at'])+3600*8)}}</span></td></tr>
                <tr><td colspan="2"><span>
                            Task:<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$result['claim']}}</i>
                            Reviewed Today:<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$result['todayCount']}}</i>
                            Total:<i style="font-weight: 600;font-size: 16px;">{{$result['totalCount']}}</i>
                            </span>
                    </td></tr>
                <tr><td colspan="2">
                        <div>
                            <form method="post" action="{{url('backstage/business/review')}}/{{$result['comment_id']}}">
                                {{ csrf_field() }}
                                <input name="audit" hidden value="refuse">
                                <input type="hidden" name="_method" value="PUT">
                                <input name="comment_id" hidden value="{{$result['comment_id']}}">
                                <button class="layui-btn" style="background-color: #63BA79" type="submit" lay-submit="">Refuse</button>
                            </form>
                        </div>
                        <div>
                            <form  method="POST" action="{{url('backstage/business/review')}}/{{$result['comment_id']}}">
                                {{ csrf_field() }}
                                {{@method_field('PUT')}}
{{--                                <input name="_method" type="hidden" value="PUT">--}}
                                <input name="audit" hidden value="pass">
                                <input name="comment_id" hidden value="{{$result['comment_id']}}">
                                <button class="layui-btn  layui-btn-warm" type="submit" lay-submit="">Pass</button>
                            </form>
                        </div>
                        <div>
                            <form method="post" action="{{url('backstage/business/review')}}/{{$result['comment_id']}}">
                                {{ csrf_field() }}
                                <input name="level" hidden value="on">
                                {{ method_field('PUT')}}
                                <input type="hidden" name="_method" value="PUT">
                                <input name="comment_id" hidden value="{{$result['comment_id']}}">
                                <button class="layui-btn  layui-btn-normal" type="submit" lay-submit="">Recommend</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr><td style="border: 0"></td></tr>
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
                photos: '.layer-photos-demo'
                //,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            });
            $(document).on("mousewheel DOMMouseScroll", ".layui-layer-phimg img", function (e) {
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
