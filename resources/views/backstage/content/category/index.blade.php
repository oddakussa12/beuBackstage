@extends('layouts.dashboard')
@section('layui-content')
<fieldset class="layui-elem-field layui-field-title">
	<legend>{{trans('category.html.category_list')}}</legend>
</fieldset>
<table class="layui-table"   lay-filter="category_table" id="category_table" >
	<thead>
		<tr>
			<th lay-data="{field:'category_id',sort: true}">{{trans('category.table.header.category_id')}}</th>
			<th lay-data="{field:'category_name',event:'category_name'}">{{trans('category.table.header.category_name')}}</th>
			<th lay-data="{field:'category_sort',edit:'text' ,sort: true}">{{trans('category.table.header.category_sort')}}</th>
			<th lay-data="{field:'category_status'}">{{trans('category.table.header.category_status')}}</th>
			<th lay-data="{field:'category_created_at'}">{{trans('common.table.header.created_at')}}</th>
			<th  lay-data="{fixed: 'right', width:150, align:'center', toolbar: '#category'}">{{trans('common.table.header.op')}}</th>
		</tr>
	</thead>
	<tbody>
		@foreach($categories as $category)
		<tr>
			<td>{{$category->category_id}}</td>
			<td>{{$category->category_name}}</td>
			<td>{{$category->category_sort}}</td>
			<td><input type="checkbox" name="category_status" value="{{$category->category_id}}" lay-skin="switch" lay-text="ON|OFF" lay-filter="category_status" {{ $category->category_status == 1 ? 'checked' : '' }}></td>
			<td>{{$category->category_created_at}}</td>
			<td></td>
		</tr>
		@endforeach
	</tbody>
</table>
<div>{{$categories->links()}}</div>

@endsection
@section('footerScripts')
	@parent
	<script>
		layui.config({
			base: "{{url('plugin/layui')}}/"
		}).extend({
			common: 'lay/modules/admin/common',
		}).use(['common' , 'table' , 'layer'], function () {
			var form = layui.form,
			layer = layui.layer,
			table = layui.table,
			common = layui.common,
			$=layui.jquery;
			table.on('tool(category_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
				var data = obj.data; //获得当前行数据
				var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
				var tr = obj.tr;
				var category_id = data.category_id;
				if(layEvent === 'delete'){
				common.confirm("{{trans('common.confirm.delete')}}" , function(){
					common.ajax("{{url('/backstage/content/category/')}}/"+ data.category_id,{'category_id':data.category_id,'category_isdel':1} , function(res){
						obj.del();
					common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 300 , 6 , 't');
					},'put');
				} , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});
				}else if(layEvent === 'category_name'){
					@if(!Auth::user()->can('content::category.update'))
					// console.log(this);
					common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", this);
					form.render();
					return false;
					@endif
					layer.open({
						type: 2,
						title: "{{trans('common.table.tool.button.edit')}}",
						shadeClose: true,
						shade: 0.8,
						area: ['80%','80%'],
						offset: 'auto',
						content: "/{{locale()}}/backstage/content/category/"+category_id+"/edit",
					});
				}
			});
			table.on('edit(category_table)', function(obj){
				var value = obj.value //得到修改后的值
				,data = obj.data //得到所在行所有键值
				,field = obj.field; //得到字段
				common.ajax("{{url('/backstage/content/category/')}}/"+ data.category_id,{'category_id':data.category_id,'category_sort':data.category_sort,'category_name':data.category_name} , function(res){
					common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
				},'put');
			});
			table.init('category_table', { //转化静态表格
				page:false,
				toolbar: '#category_toolbar'
			});

			table.on('toolbar(category_table)', function(obj){
				// var checkStatus = table.checkStatus(obj.config.id);
				switch(obj.event){
					case 'create':
						@if(!Auth::user()->can('content::category.update'))
							common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", this);
						break;
						@endif
						common.open("{{url(locale().'/backstage/content/category/create')}}" , {area: ['40%','60%']});
						break;
				};
			});
			form.on('switch(category_status)', function(obj){
				var checked = obj.elem.checked;
				obj.elem.checked = !checked;
				@if(!Auth::user()->can('content::category.update'))
				common.tips("{{trans('common.ajax.result.prompt.no_permission')}}", obj.othis);
				form.render();
				return false;
				@endif
				form.render();
				var name = $(obj.elem).attr('name');
				var category_status = checked?1:0;
				var category_id = obj.value;
				var params ='{"'+name+'":'+category_status+'}';
				common.confirm("{{trans('common.confirm.update')}}" , function(){
					console.log(typeof (checked));
					common.ajax("{{url('/backstage/content/category/')}}/"+category_id , JSON.parse(params), function(res){
						obj.elem.checked = checked;
						form.render();
						common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
					} , 'put' , function (event,xhr,options,exc) {
						setTimeout(function(){
							common.init_error(event,xhr,options,exc);
							obj.elem.checked = !checked;
							form.render();
						},100);
					});
				} , {btn:["{{trans('common.confirm.yes')}}" , "{{trans('common.confirm.cancel')}}"]});

			});
		});
	</script>
	<script type="text/html" id="category_toolbar">
		<div class="layui-btn-container">
			<button class="layui-btn layui-btn-sm" lay-event="create">{{trans('common.table.tool.button.create')}}</button>
		</div>
	</script>
	<script type="text/html" id="category">
		<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>
	</script>
@endsection