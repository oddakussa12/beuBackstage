@extends('layouts.app')
@section('content')
    <div class="layui-fluid">
        <div class="layui-tab" lay-filter="translation">
            <ul class="layui-tab-title">
                @foreach($posts as $post)
                <li  lay-id="{{$post->post_translation_id}}">{{$post->post_locale}}</li>
                @endforeach
            </ul>
            <div class="layui-tab-content">
                @foreach($posts as $post)

                <div class="layui-tab-item layui-container">
                    <form class="layui-form" method="post" action="{{route('content::post.translation.update' , array($post->post_id , $post->post_translation_id))}}">
                        <div class="layui-form-item layui-form-text">
                            <div class="layui-input-block">
                                <textarea style="min-height:300px;max-height:400px;" name="post_content" placeholder="{{trans('common.form.placeholder.blank')}}" class="layui-textarea">{!!$post->post_content!!}</textarea>
                            </div>
                        </div>
                        {{ csrf_field() }}
                        <input type="hidden" name="post_translation_id" value="{{$post->post_translation_id}}"/>
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.update')}}</button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
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
            var $ = layui.jquery,
                element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
            var layid = location.hash.replace(/^#translation=/, '');
            element.tabChange('translation', layid);
            element.on('tab(translation)', function(elem){
                location.hash = 'translation='+ $(this).attr('lay-id');
            });
        });
    </script>

@endsection