@extends('layouts.app')
@section('content')
    <div  class="layui-fluid">
        <table class="layui-hide" id="user_table" lay-filter="user_table">
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
            timePicker: 'lay/modules/admin/timePicker',
        }).use(['common' , 'table' , 'layer' , 'flow' , 'laydate' , 'timePicker'], function () {
            var $ = layui.jquery,
                table = layui.table,
                form = layui.form,
                common = layui.common,
                layer = layui.layer,
                flow = layui.flow,
                timePicker = layui.timePicker,
                laydate = layui.laydate,
                isLoaded = false;


            var tableIns = table.render({
                elem: '#user_table'
                ,url:'/backstage/passport/user/yesterday'
                // ,toolbar: true
                ,cols: [[
                    {field:'title', title:'Title', maxWidth:120,style:'font-size: 20px;font-weight:bold;color:#393D49;'}
                    ,{field:'date', title:'Date', maxWidth:120,templet: function(d){
                            return '<span style="color: #393D49;font-size: 30px;font-weight:bold;">'+ d.date +'</span>'
                      }
                    }
                    ,{field:'value', title:'Value', minWidth:120,templet: function(d){
                            return '<span style="color: #393D49;font-size: 30px;font-weight:bold;">'+ d.value +'</span>'
                        }
                    }
                ]]
                ,page: false
                ,response: {
                    statusCode: 200
                }
                ,parseData: function(res){
                    console.log(res);
                    var item = [];
                    item.push({
                        'title':"DAU",
                        'date':res.dau.date,
                        'value':res.dau.dau.dau,
                    });
                    item.push({
                        'title':"Sign Up",
                        'date':res.new.date,
                        'value':res.new.new.new,
                    });
                    item.push({
                        'title':"One day retention",
                        'date':res.keep.one.one+'=>'+res.keep.date,
                        'value':(res.keep.one.oneKeep.one*100/res.keep.one.oneKeep.new).toFixed(2)+"%",
                    });
                    item.push({
                        'title':"Two day retention",
                        'date':res.keep.two.two+'=>'+res.keep.date,
                        'value':(res.keep.two.twoKeep.two*100/res.keep.two.twoKeep.new).toFixed(2)+"%",
                    });
                    item.push({
                        'title':"Three day retention",
                        'date':res.keep.three.three+'=>'+res.keep.date,
                        'value':(res.keep.three.threeKeep.three*100/res.keep.three.threeKeep.new).toFixed(2)+"%",
                    });
                    item.push({
                        'title':"Seven day retention",
                        'date':res.keep.seven.seven+'=>'+res.keep.date,
                        'value':(res.keep.seven.sevenKeep.seven*100/res.keep.seven.sevenKeep.new).toFixed(2)+"%",
                    });
                    item.push({
                        'title':"Fourteen day retention",
                        'date':res.keep.fourteen.fourteen+'=>'+res.keep.date,
                        'value':(res.keep.fourteen.fourteenKeep.fourteen*100/res.keep.fourteen.fourteenKeep.new).toFixed(2)+"%",
                    });
                    item.push({
                        'title':"Thirty day retention",
                        'date':res.keep.thirty.thirty+'=>'+res.keep.date,
                        'value':(res.keep.thirty.thirtyKeep.thirty*100/res.keep.thirty.thirtyKeep.new).toFixed(2)+"%",
                    });
                    console.log(item);
                    return {
                        "code": 200,
                        "data": item
                    };
                },
                done:function(res, curr, count){
                    isLoaded = true;
                }
            });

            setInterval(function() {
                if(isLoaded)
                {
                    tableIns.reload();
                }else{
                    console.log(111);
                }
            }, 200000);
            @if($jump==1)
            setTimeout(function() {
                location.href="http://62.234.26.29:8000/dau";
            }, 60000);
            @endif
        })
    </script>
    <style>
        .layui-table-cell {
            height: 60px;
            line-height: 60px;
        }
        .multi dl dd.layui-this{background-color:#fff}
    </style>
@endsection
