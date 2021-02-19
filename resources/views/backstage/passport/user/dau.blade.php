@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form" action="" lay-filter="keep">
            <div class="layui-form-item">
                <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                <div class="layui-inline">
                    <select name="country_code" lay-verify="" lay-search>
                        <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                        @foreach($counties  as $country)
                            <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">{{trans('user.form.label.date')}}:</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" name="period" id="period" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$period}}">
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                </div>
            </div>
        </form>
        <table class="layui-table" >
            <thead>
            <tr>
                <th>Day</th>
                <th>DUA</th>
                <th>0NUM</th>
                <th>0NUM%</th>
                <th>1NUM</th>
                <th>1NUM%</th>
                <th>2NUM</th>
                <th>2NUM%</th>
                <th>>3NUM</th>
                <th>>3NUM%</th>
            </tr>
            </thead>

        </table>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script type="text/html" id="operateTpl">
        <div class="layui-table-cell laytable-cell-1-6">
            <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">{{trans('common.table.button.edit')}}</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">{{trans('common.table.button.delete')}}</a>
        </div>
    </script>

    <script>
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer,
                flow = layui.flow,
                laydate = layui.laydate;

            laydate.render({
                elem: '#period'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });


            table.init('user_table', { //转化静态表格
                page:false
            });
        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
