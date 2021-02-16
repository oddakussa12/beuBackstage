@extends('layouts.dashboard_nofoot')
@section('layui-content')
    <style>
        .layui-body {height: calc(90vh); overflow: hidden;}
        #container {
            width: 102%;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            clear: both;
            /*padding-bottom: 100px;*/
            height: calc(66vh);
        }
        .layui-table img{ min-width:100px; max-width: 300px;}
        table div{width: 20%; float: left;}
        .checkbox { margin-left: 50px; transform: scale(2.5); vertical-align: middle;}
    </style>
    <div  class="layui-fluid">
        <form id="form-refuse" method="post">
            {{ csrf_field() }}
            <input id="type" type="hidden" name="type" @if(!empty($result['query']['type'])) value="{{$result['query']['type']}}" @else value="image" @endif>
            <input id="from" type="hidden" name="from" @if(!empty($result['from'])) value="{{$result['from']}}" @else value="" @endif>
            <input id="lastId" type="hidden" name="lastId" @if(!empty($result['lastId'])) value="{{$result['lastId']}}" @else value="" @endif>
            <input id="user_id" type="hidden" name="user_id" @if(!empty($result['query']['user_id'])) value="{{$result['query']['user_id']}}" @else value="" @endif>
            <table class="layui-table" lay-filter="post_table">
                @if (empty($result))
                    <tr><td style="text-align: center; height: 500px; font-size: 18px;">真厉害，都审核完成了呢！休息一下吧☺☺</td></tr>
                @else
                    <tr><th>
                            <div>
                                <button class="layui-btn" style="background-color: #63BA79" name="status" value="refuse" type="submit" lay-submit="">不通过</button>
                            </div>
                            <div>
                                <button class="layui-btn  layui-btn-warm" name="status" value="hot"  type="submit" lay-submit="">通过并上热门</button>
                            </div>
                            <div>
                                <button class="layui-btn layui-btn-danger"  name="status" value="preheat" type="submit" lay-submit="">通过并上热门预热</button>
                            </div>
                            <div>
                                <input name="post_hotting" hidden value="off">
                                <button class="layui-btn" type="submit" lay-tips="通过不上热门" name="status" value="pass" lay-submit="">通过不上热门</button>
                            </div>
                            <div style="position: absolute;margin-left: 80%; line-height: 40px;">
{{--                                <div class="layui-input-inline">--}}
{{--                                    <select name="type">--}}
{{--                                    <option value="image" @if(isset($result['query']['type']) && $result['query']['type']=='image') selected @endif>图片</option>--}}
{{--                                    <option value="video" @if(isset($result['query']['type']) && $result['query']['type']=='video') selected @endif>视频</option>--}}
{{--                                    <option value="text"  @if(isset($result['query']['type']) && $result['query']['type']=='text')  selected @endif>文本</option>--}}
{{--                                    <option value="all"   @if(isset($result['query']['type']) && $result['query']['type']=='all')   selected @endif>全部</option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
                            <span class="layui-btn layui-btn-normal" style="display: block; cursor:default">
                                待审核数量：<i style="font-weight: 600;font-size: 16px;">{{$result['total']}}</i></span>
                            </div>
                        </th>
                    </tr>
                @endif
            </table>
            <div class="layui-container layui-form">
                <button class="layui-btn" id="quanxuan">全选</button>
                <button class="layui-btn" id="fanxuan">反选</button>
                <button class="layui-btn" id="buxuan">不选</button>
                <br><br>
            </div>
            <div class="layui-container" id="container"> </div>
        </form>
    </div>

@endsection
@section('footerScripts')
    @parent
    <script>
        layui.use(['form','element','jquery','layer','flow'], function() {
            var $ = layui.jquery;
            var flow = layui.flow;
            var form = layui.form;

            flow.lazyimg();

            console.log('初始化');
            flow.load({
                elem: '#container', //流加载容器
                scrollElem: '#container',
                isAuto: true

                //滚动条所在元素，一般不用填，此处只是演示需要。
                ,done: function(page, next){ //执行下一页的回调
                    console.log(page)
                    //模拟数据插入
                    setTimeout(function(){
                        let url;
                        var list = [];
                        var from = $("#from").val();
                        var type = $("#type").val();
                        var user = $("#user_id").val();

                        url = from == 'post' ? "{{url('/backstage/content/audit/flow')}}" : "{{url('/backstage/report/report/post/flow')}}";
                        url = url+"?type="+type+"&user_id="+user+"&page="+page;

                        $.get(url,function (res) {
                            if (res.data=='') {
                                alert('已无数据');
                                return false;
                            }

                            console.log(res.data[res.data.length-1].post_id);
                            $("lastId").val(res.data[res.data.length-1].post_id);
                            layui.each(res.data, function(index, item) {
                                list.push('<div class="layui-row list bd" style="min-height: 170px;">\
                                <span class="ids" style="display: none">+item.post_id+</span>\
                                <div style="width: 100%" class="layui-col-xs4"><div style="float: left">');
                                if (item.post_type=='image' || item.post_type=='video') {
                                    if (item.post_type=='image') {
                                        layui.each(item.media.image.thumb_image_url, function(index, img) {
                                            list.push('<img style="height:250px; padding-right: 5px;" title="这里有图片" src="' + img+'"/>');
                                        });
                                    } else {
                                        list.push('<video controls="controls" height="300px" poster="'+item.media.video.video_thumbnail_url+'" >\
                                        <source src="'+item.media.video.video_url+'" type="video/mp4" />\
                                        Your browser does not support the video tag.\
                                    </video>');
                                    }
                                }

                                list.push('</div>\
                                <div style="width:200px;float:right;position: absolute;right: 0px;z-index:100">\
                                <p><a target="_blank" class="layui-btn" style="padding:0 5px" href="{{route('content::post.index')}}?user_id='+item.user_id+'">帖子列表页</a>\
                                <input type="checkbox" class="checkbox" name="id[]" value="'+item.post_id+'"></p><br>\
                                <p><a target="_blank" class="layui-btn" href="{{url('/backstage/content/audit')}}?id='+item.post_id+'">点击跳转审核页</a></p><br>\
                                <p><a target="_blank" class="layui-btn" href="https://web.yooul.com/detail?post_uuid='+item.post_uuid+'">点击跳转WEB页</a></p>\
                                </div>\
                                <div class="layui-col-xs7 layui-col-sm12 right">\
                                <br/><p>原始语言：'+item.post_content+'</p>\
                                <p>中文：'+item.trans.cn+'</p>\
                                <p>英文：'+item.trans.en+'</p>\
                                <br/>\
                            </div>\
                        </div></div>');
                            });//组装html

                            //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                            if (page <= res.total) {
                                next(list.join(''), page <= res.total);
                            } else {
                                alert('没有更多了');
                            }
                        })
                    }, 300);
                }
            });

            // form.on('checkbox(checkall)', function(data){
            //     $("input[name='id[]']").prop("checked",data.elem.checked);//prop 可以此次生效
            //     //$("input[name='e_data[]']").attr("checked",false);//使用这个只能生效一次 PASS
            //     form.render();//这个地方必须加上重新渲染 否则没有状态变化
            // });

            $("#quanxuan").click(function(){//全选
                $(".bd input[type='checkbox']").prop("checked",true);
                form.render('checkbox');
                return false;
            });

            $("#fanxuan").click(function(){//反选
                $(".bd input[type='checkbox']").each(function(){
                    if($(this).prop("checked")){
                        $(this).prop("checked",false);
                    }else{
                        $(this).prop("checked",true);
                    }
                });
                form.render('checkbox');
                return false;
            });

            $("#buxuan").click(function(){//不选
                $(".bd input[type='checkbox']").prop("checked",false);
                form.render("checkbox");
                return false;
            });
        });
    </script>
@endsection