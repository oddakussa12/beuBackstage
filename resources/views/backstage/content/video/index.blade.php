@extends('layouts.dashboard')
@section('layui-content')
	<fieldset class="layui-elem-field layui-field-title">
	  <legend>{{trans('video.html.video_list')}}</legend>
	</fieldset>
	<table class="layui-table"   lay-filter="video_table" id="video_table" >

	  <thead>
	    <tr>
	      <th lay-data="{field:'video_id', width:100}">{{trans('video.table.header.video_id')}}</th>
	      <th lay-data="{field:'video_uuid', width:100}">{{trans('video.table.header.video_uuid')}}</th>
	      <th lay-data="{field:'video_created_at', width:100}">{{trans('common.table.header.created_at')}}</th>
	      <th  lay-data="{fixed: 'right', width:150, align:'center', toolbar: '#videoop'}">{{trans('common.table.header.op')}}</th>
	    </tr> 
	  </thead>
	  <tbody>
	  	@foreach($list as $value)
		    <tr>
		      <td>{{$value->video_id}}</td>
		      <td>{{$value->video_uuid}}</td>
		      <td>{{$value->video_created_at}}</td>
		      <td></td>
		    </tr>
	    @endforeach
	  </tbody>
	</table>
@endsection
@section('footerScripts')
    @parent
    <script>
    	//内容修改弹窗
		layui.config({
			base: "{{url('plugin/layui')}}/"
		}).extend({
			common: 'lay/modules/admin/common',
		}).use(['common' , 'table' , 'layer'], function () {
			var form = layui.form,
			layer = layui.layer,
			table = layui.table,

			$=layui.jquery;
			$(document).on('click','#create',function(){
				layer.open({
                    type: 2,
                    title: '添加',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['80%','80%'],
                    offset: 'auto',
                    content: '/backstage/content/video/create',
                });
			});
			table.on('tool(video_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
			  var data = obj.data; //获得当前行数据
			  var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
			  var tr = obj.tr; //获得当前行 tr 的DOM对象
			  if(layEvent === 'check'){ //查看
			    //do somehing
			    alert('check');
			  } else if(layEvent === 'delete'){ //删除
			    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
			      obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
			      layer.close(index);
			      //向服务端发送删除指令
			    });
			  } else if(layEvent === 'edit'){ //编辑
			  	console.log(data);
			  	var video_id = data.video_id;
				layer.open({
					type: 2,
					title: '添加',
					shadeClose: true,
					shade: 0.8,
					area: ['80%','80%'],
					offset: 'auto',
					content: '/backstage/content/video/'+video_id+'/edit',
				});
			    //do something
			    //同步更新缓存对应的值
			    obj.update({
			      username: '123'
			      ,title: 'xxx'
			    });
			  }
			});
			table.init('video_table', { //转化静态表格
				page:false,
				toolbar: '#toolbarDemo'
			});
			table.on('toolbar(video_table)', function(obj){
			  var checkStatus = table.checkStatus(obj.config.id);
			  var layEvent = obj.event;
			  if(layEvent === 'update'){
			  	alert('edit')
			  }
			  switch(obj.event){
			    case 'create':
			      layer.msg('添加');
			    break;
			    case 'delete':
			      layer.msg('删除');
			    break;
			    case 'update':
			      layer.msg('编辑');
			    break;
			  };
			});
		});
    </script>
	<script type="text/html" id="toolbarDemo">
	  <div class="layui-btn-container">
	    <button class="layui-btn layui-btn-sm" id="create">{{trans('common.table.tool.button.create')}}</button>
	  </div>
	</script>
	<script type="text/html" id="videoop">
	  <a class="layui-btn layui-btn-xs" lay-event="check">{{trans('common.table.button.check')}}</a>
	  <a class="layui-btn layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
	  <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{trans('common.table.button.delete')}}</a>
	</script>
@endsection