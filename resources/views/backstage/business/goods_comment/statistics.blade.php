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
            @if (empty($comment))
                @if(empty($pendingReview))
                <tr><td colspan="2"><h5>Amazing! The audit is all done! Take a break ☺☺</h5><br></td></tr>
                @else
                    <tr><td colspan="2">
                            <h5>To Be Reviewed:<i style="font-weight: 600;font-size: 16px;">{{$pendingReview}}</i></h5><br>
                            <form method="get" action="{{route('business::goods_comment.acquisition')}}">
                                <button class="layui-btn layui-btn-normal" type="submit">Continue to review</button>
                            </form>
                        </td></tr>
                @endif
                    <tr><td>Reviewed Today:</td><td><i style="font-weight: 600;font-size: 16px;">{{$todayCount}}</i></td></tr>
                    <tr><td>Total:</td><td><i style="font-weight: 600;font-size: 16px;">{{$todayCount}}</i></td></tr>
            @else
                <tr><td>ShopName:</td><td><span>{{$comment->shop->user_nick_name}}</span></td></tr>
                <tr><td>GoodsName:</td><td><span>{{$comment->goods->name}}</span></td></tr>
                <tr><td>GoodsImage:</td><td><span>
                            <div class="layer-photos-demo">
                                @if(!empty($comment->goods->image))
                                @foreach($comment->goods->image as $image)
                                    <img style="min-width: 30px; max-width: 40px;" src="{{$image['url']}}">
                                @endforeach
                                @endif
                            </div>
                        </span>
                    </td></tr>
                <tr><td>Point:</td><td><span>{{$comment->goods->average_point}}</span></td></tr>
                <tr><td>Service:</td><td><span>{{$comment->goods->service}}</span></td></tr>
                <tr><td>Quality:</td><td><span>{{$comment->goods->quality}}</span></td></tr>
                <tr><td>Content:</td><td><span>{{$comment->content}}</span></td></tr>
                <tr><td colspan="2">
                        @if (!empty($comment->media))
                            <div class="layer-photos-demo">
                                @foreach($comment->media as $media)
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
                    <td><span>{{date('Y-m-d H:i:s',strtotime($comment->created_at)+3600*8)}}</span></td></tr>
                <tr><td colspan="2"><span>
                            Task:<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$claim}}</i>
                            Reviewed Today:<i style="font-weight: 600;font-size: 16px;margin-right: 10px;">{{$todayCount}}</i>
                            Total:<i style="font-weight: 600;font-size: 16px;">{{$todayCount}}</i>
                            </span>
                    </td></tr>
                <tr><td colspan="2">
                        <div>
                            <form method="post" action="{{url('backstage/business/goods_comment')}}/{{$comment->comment_id}}">
                                {{ csrf_field() }}
                                {{ method_field('PUT')}}
                                <input name="verified" hidden value="no">
                                <button class="layui-btn" style="background-color: #63BA79" type="submit" lay-submit="">Refuse</button>
                            </form>
                        </div>
                        <div>
                            <form method="post" action="{{url('backstage/business/goods_comment')}}/{{$comment->comment_id}}">
                                {{ csrf_field() }}
                                {{ method_field('PUT')}}
                                <input name="verified" hidden value="yes">
                                <button class="layui-btn  layui-btn-warm" type="submit" lay-submit="">Pass</button>
                            </form>
                        </div>
                        <div>
                            <form method="post" action="{{url('backstage/business/goods_comment')}}/{{$comment->comment_id}}">
                                {{ csrf_field() }}
                                {{ method_field('PUT')}}
                                <input name="level" hidden value="on">
                                <input name="verified" hidden value="yes">
                                <button class="layui-btn  layui-btn-normal" type="submit" lay-submit="">Essence</button>
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
            });
        })

    </script>
@endsection
