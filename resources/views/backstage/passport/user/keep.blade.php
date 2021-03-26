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
        <table class="layui-table">
            <thead>
            <tr>
                <th>Day</th>
                <th>SignUp</th>
                <th>1Day</th>
                <th>1Day%</th>
                <th>2Day</th>
                <th>2Day%</th>
                <th>3Day</th>
                <th>3Day%</th>
                <th>7Day</th>
                <th>7Day%</th>
                <th>30Day</th>
                <th>30Day%</th>
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
            // table.init('common_table', {
            //     page:false,
            // });

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
