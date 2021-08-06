@extends('layouts.dashboard')
<style>
    .layui-fluid {
        height: 96%;
    }
</style>
@section('layui-content')
    <div  class="layui-fluid">
        <div class="layui-row" style="padding: 10px;">
            <div class="layui-btn-group">
                @foreach($iframes as $k=>$v)
                    <a class="layui-btn layui-btn-normal layui-btn-xs @if($type==$k) layui-btn-disabled @endif" href="?type={{$k}}&promo_code={{$promo_code}}" >{{trans('business.form.label.complex.'.$k)}}</a>
                @endforeach
            </div>
        </div>
        <div class="layui-row">
            <iframe src="{{$iframe}}" frameborder="0" style="height: 100%;width: 100%;"></iframe>
        </div>
    </div>
@endsection
