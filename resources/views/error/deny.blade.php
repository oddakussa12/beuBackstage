@extends('layouts.app')
@section('content')
    
    <div class="layui-fluid">
        <div class="layadmin-tips">
            <i class="layui-icon" face>&#xe664;</i>

            <div class="layui-text" style="font-size: 20px;">
                {{$error}}
            </div>

        </div>
    </div>


@endsection


@section('footerScripts')
    @parent
    <style>
        .layui-fluid {
            padding: 15px
        }
        .layadmin-tips {
            margin-top: 30px;
            text-align: center
        }

        .layadmin-tips .layui-icon[face] {
            display: inline-block;
            font-size: 300px;
            color: #393D49
        }

        .layadmin-tips .layui-text {
            width: 500px;
            margin: 30px auto;
            padding-top: 20px;
            border-top: 5px solid #009688;
            font-size: 16px
        }

        .layadmin-tips h1 {
            font-size: 100px;
            line-height: 100px;
            color: #009688
        }

        .layadmin-tips .layui-text .layui-anim {
            display: inline-block
        }

        .layadmin-tips .layui-text {
            width: 500px;
            margin: 30px auto;
            padding-top: 20px;
            border-top: 5px solid #009688;
            font-size: 16px
        }

        .layadmin-tips h1 {
            font-size: 100px;
            line-height: 100px;
            color: #009688
        }

        .layadmin-tips .layui-text .layui-anim {
            display: inline-block
        }
    </style>
    <script>
        // layui.config({
        //     base: '../../../layuiadmin/' //静态资源所在路径
        // }).extend({
        //     index: 'lib/index' //主入口模块
        // }).use(['index']);
    </script>
@endsection





