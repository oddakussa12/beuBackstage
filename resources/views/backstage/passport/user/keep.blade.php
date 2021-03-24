@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <form class="layui-form" action="" lay-filter="keep">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('user.form.label.user_country')}}:</label>
                    <div class="layui-input-inline">
                        <select name="country_code" lay-verify="" lay-search>
                            <option value="">{{trans('user.form.placeholder.user_country')}}</option>
                            @if($v==0))
                            <option value="all" @if($country_code=='all')) selected @endif>ALL</option>
                            @endif
                            @foreach($countries  as $country)
                                <option value="{{strtolower($country['code'])}}" @if($country_code==strtolower($country['code'])) selected @endif>{{$country['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{trans('common.form.label.date')}}:</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="period" id="period" readonly="" placeholder="yyyy-MM-dd - yyyy-MM-dd" value="{{$period}}">
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
                    </div>
                </div>

            </div>
        </form>
        <table class="layui-table" lay-filter="common_table" >
            <thead>
            <tr>
                <th lay-data="{field:'Day', width:120 ,fixed: 'left'}">Day</th>
                <th lay-data="{field:'1Day', minWidth:100}">SignUp</th>
                <th lay-data="{field:'1Day', width:100}">1Day</th>
                <th lay-data="{field:'1Day%', width:100}">1Day%</th>
                <th lay-data="{field:'2Day', width:100}">2Day</th>
                <th lay-data="{field:'2Day%', width:100}">2Day%</th>
                <th lay-data="{field:'3Day', width:100}">3Day</th>
                <th lay-data="{field:'3Day%', width:100}">3Day%</th>
                <th lay-data="{field:'7Day', width:100}">7Day</th>
                <th lay-data="{field:'7Day%', width:100}">7Day%</th>
                <th lay-data="{field:'30Day', width:100}">30Day</th>
                <th lay-data="{field:'30Day%', width:100}">30Day%</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $d=>$l)
                <tr>
                    <td>{{$d}}</td>
                    <td>{{$l['num']}}</td>
                    <td>{{$l['tomorrowNum']}}</td>
                    @if ($l['num']==0)
                    <td>0%</td>
                    @else
                    <td>{{round($l['tomorrowNum']/$l['num']*100 , 2)}}%</td>
                    @endif

                    <td>{{$l['twoNum']}}</td>
                    @if ($l['num']==0)
                    <td>0%</td>
                    @else
                    <td>{{round($l['twoNum']/$l['num']*100 , 2)}}%</td>
                    @endif

                    <td>{{$l['threeNum']}}</td>
                    @if ($l['num']==0)
                    <td>0%</td>
                    @else
                    <td>{{round($l['threeNum']/$l['num']*100 , 2)}}%</td>
                    @endif

                    <td>{{$l['sevenNum']}}</td>
                    @if ($l['num']==0)
                        <td>0%</td>
                    @else
                    <td>{{round($l['sevenNum']/$l['num']*100 , 2)}}%</td>
                    @endif

                    <td>{{$l['thirtyNum']}}</td>
                    @if ($l['num']==0)
                        <td>0%</td>
                    @else
                    <td>{{round($l['thirtyNum']/$l['num']*100 , 2)}}%</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
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
        }).use(['common' , 'table' , 'laydate'], function () {
            var $ = layui.jquery,
                table = layui.table,
                laydate = layui.laydate;
            laydate.render({
                elem: '#period'
                ,range: true
                ,max : 'today'
                ,lang: 'en'
            });
            table.init('common_table', {
                page:false,
            });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
