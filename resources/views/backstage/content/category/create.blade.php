@extends('layouts.app')
@php
    $supportedLocales = LaravelLocalization::getSupportedLocales();
@endphp
@section('content')
<form class="layui-form" action="">
<div class="layui-form-item" style="margin-top: 3%">
</div>
	@if(count($supportedLocales)>=1)
		@foreach($supportedLocales as $localeCode => $properties)
		<div class="layui-form-item">
			<label class="layui-form-label">{!! $properties['native'] !!}</label>
			<div class="layui-input-block" style="width:50%">
				<input type="text" name="{{$localeCode}}_category_name" required  lay-verify="required" placeholder="{{trans('category.form.placeholder.category_name')}}" autocomplete="off" class="layui-input">
			</div>
		</div>
		@endforeach
	@endif
	<div class="layui-form-item">
		<label class="layui-form-label">{{trans('category.form.form_category_sort')}}</label>
		<div class="layui-input-inline">
			<input type="text" name="category_sort" required lay-verify="required" placeholder="{{trans('category.form.placeholder.category_sort')}}" autocomplete="off" class="layui-input" value="1">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">{{trans('category.form.form_category_status')}}</label>
		<div class="layui-input-block">
			<input type="checkbox" checked="" name="category_status" lay-skin="switch" lay-text="ON|OFF">
		</div>
	</div>
	<div class="layui-form-item">
		<div class="layui-input-block">
			<button class="layui-btn" lay-submit lay-filter="categorysubmit">{{trans('common.form.button.add')}}</button>
			<button type="reset" class="layui-btn layui-btn-primary">{{trans('common.form.button.reset')}}</button>
		</div>
	</div>
</form>
@endsection
@section('footerScripts')
	@parent
	<script>
	layui.config({base: "{{url('plugin/layui')}}/"}).extend({common: 'lay/modules/admin/common',}).use(['common' , 'table' , 'layer'], function () {
		var $ = layui.jquery,
		form = layui.form,
		common = layui.common;
		var supportedLocales = @json($supportedLocales);
		form.on('submit(categorysubmit)', function(data){
			var params = common.fieldToArr(data.field , supportedLocales);
			common.ajax("{{url('/backstage/content/category/')}}" , params , function(res){
				window.parent.location.reload();
			});
			return false;
		});
	});
	</script>
@endsection