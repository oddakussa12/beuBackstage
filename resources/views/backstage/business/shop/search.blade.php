@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.keyword')}}:</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" name="keyword" placeholder="{{trans('common.form.label.keyword')}}" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline" >
                        <input type="text" class="layui-input" name="dateTime" id="dateTime" readonly placeholder=" - " @if(!empty($dateTime)) value="{{$dateTime}}" @endif>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    </div>
                </div>

            </div>
        </form>

        <table class="layui-table" lay-filter="table" id="table">
            <thead>
            <tr>
                <th lay-data="{field:'content', minWidth:180}">{{trans('common.form.label.keyword')}}</th>
                <th lay-data="{field:'contentCount', minWidth:180}">{{trans('common.form.label.num')}}</th>
                <th lay-data="{field:'userCount', minWidth:180}">{{trans('user.table.header.user_count')}}</th>
                <th  lay-data="{field:'user_op', width:100 ,fixed: 'right', templet: '#op'}">{{trans('common.table.header.op')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($searches as $value)
                <tr>
                    <td>{{$value->content}}</td>
                    <td>{{$value->contentCount}}</td>
                    <td>{{$value->userCount}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(empty($appends))
            {{ $searches->links('vendor.pagination.default') }}
        @else
            {{ $searches->appends($appends)->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //??????????????????
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const table = layui.table,
                common = layui.common,
                timePicker = layui.timePicker;
            table.init('table', { //??????????????????
                page:false,
                toolbar: '#toolbar'
            });
            timePicker.render({
                elem: '#dateTime',
                options:{
                    timeStamp:false,
                    format:'YYYY-MM-DD HH:ss:mm',
                    locale:"{{locale()}}"
                },
            });
            table.on('tool(table)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"
                let data = obj.data; //?????????????????????
                let layEvent = obj.event; //?????? lay-event ???????????????????????????????????? event ?????????????????????
                if(layEvent === 'view'){
                    common.open_page("{{LaravelLocalization::localizeUrl('/backstage/business/shop/search')}}/"+data.content);
                }
            });
        });
    </script>
    <script type="text/html" id="op">
        <a class="layui-btn layui-btn-xs" lay-event="view">{{trans('common.table.button.detail')}}</a>
    </script>
@endsection