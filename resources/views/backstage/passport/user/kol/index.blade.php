@extends('layouts.dashboard')
@section('layui-content')
    <div class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="button" id="add" lay-submit >{{trans('common.form.button.add')}}</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="layui-table"  lay-filter="table">
            <thead>
            <tr>
                <th  lay-data="{field:'user_id', width:110 ,fixed: 'left'}">{{trans('user.table.header.user_id')}}</th>
                <th  lay-data="{field:'user_nick_name', minWidth:150}">{{trans('user.table.header.user_nick_name')}}</th>
                <th  lay-data="{field:'user_name', minWidth:190}">{{trans('user.table.header.user_name')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kol as $k)
                <tr>
                    <td>{{$k->user->user_id}}</td>
                    <td>{{$k->user->user_nick_name}}</td>
                    <td>{{$k->user->user_name}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $kol->links('vendor.pagination.default') }}
        @else
            {{ $kol->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
            echarts: 'lay/modules/echarts',
        }).use(['element', 'common' , 'table'], function () {
            let $ = layui.jquery,
                element = layui.element,
                table = layui.table;
            table.init('table', { //转化静态表格
                page:false,
                toolbar: '#toolbar'
            });
            $(document).on('click','#add',function(){
                layer.open({
                    type: 2,
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%','100%'],
                    offset: 'auto',
                    content: '/backstage/passport/user/kol/create',
                });
            });
        })
    </script>
@endsection
