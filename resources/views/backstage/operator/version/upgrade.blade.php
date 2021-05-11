@extends('layouts.dashboard')
@section('layui-content')
    <table class="layui-table" lay-filter="table">
        <thead>
        <tr>
            <th lay-data="{field:'field', width:0 ,hide: true}">Field</th>
            <th lay-data="{field:'Key', width:120 ,fixed: 'left'}">Key</th>
            <th lay-data="{field:'Value', minWidth:100, event: 'edit'}">Value</th>
        </tr>
        </thead>
        <tbody>
        @foreach($version as $k=>$v)
            <tr>
                <td>{{$v['field']}}</td>
                <td>{{$k}}</td>
                <td>{!! $v['value'] !!}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © {{ trans('common.company_name') }}
    </div>
@endsection
@section('footerScripts')
    @parent

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['element','common', 'table'], function () {
            let $ = layui.jquery,
                table = layui.table,
                common = layui.common;
            table.init('table', {
                page:false
            });

            table.on('tool(table)', function(obj){
                @if(!Auth::user()->can('operator::version.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}" , $(this));
                table.render();
                return true;
                @endif
                var data = obj.data;
                console.log(data);
                var field = data.field;
                var value = data.Value;
                layer.prompt({
                    formType: 2,
                    value: value.replace(/\\n/g , "\r\n"),
                    area: ['500px', '300px'] //自定义文本域宽高
                }, function(value, index, elem){
                    layer.close(index);
                    var params = {};
                    var v = value.replace(/↵/g , "\\n");
                    params[field] = v;
                    console.log(params);
                    common.ajax("{{url('/backstage/operator/version')}}/"+{{$id}} , params , function(res){
                        common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    } , 'patch');
                    obj.update({
                        'Value': v
                    });
                });
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
