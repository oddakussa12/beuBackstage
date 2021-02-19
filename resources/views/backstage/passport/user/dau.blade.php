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
            @foreach($list as $l)
                <tr>
                    <td>{{$l->date}}</td>
                    <td>{{$l->dau}}</td>
                    <td>{{$l->zero}}</td>
                    @if ($l->dau==0)
                    <td>0%</td>
                    @else
                    <td>{{round($l->zero/$l->dau*100 , 2)}}%</td>
                    @endif

                    <td>{{$l->one}}</td>
                    @if ($l->dau==0)
                        <td>0%</td>
                    @else
                        <td>{{round($l->one/$l->dau*100 , 2)}}%</td>
                    @endif

                    <td>{{$l->two}}</td>
                    @if ($l->dau==0)
                        <td>0%</td>
                    @else
                        <td>{{round($l->two/$l->dau*100 , 2)}}%</td>
                    @endif

                    <td>{{$l->gt3}}</td>
                    @if ($l->dau==0)
                        <td>0%</td>
                    @else
                        <td>{{round($l->gt3/$l->dau*100 , 2)}}%</td>
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

            table.on('tool(user_table)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm("{{trans('common.confirm.delete')}}", function(index){
                        common.ajax("{{url('/backstage/permission')}}/"+data.id , {} , function(res){
                            common.prompt("{{trans('common.ajax.result.prompt.delete')}}" , 1 , 500 , 6 , 't' ,function () {
                                // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                // layer.close(index);
                                location.reload();
                            });
                        } , 'delete');


                        //向服务端发送删除指令
                    });
                } else if(layEvent === 'edit'){ //编辑
                    location.href='/{{app()->getLocale()}}/backstage/passport/user/'+data.user_id+'/edit';
                }
            });
            flow.lazyimg();

        })
    </script>
    <style>
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
