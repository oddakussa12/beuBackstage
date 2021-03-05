@extends('layouts.app')
<!-- 标题 -->
@section('title', trans('login.title'))

@section('content')
    <link rel="stylesheet" href="{{ asset('plugin/layui/style/login.css') }}" media="all">
    <div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">

        <div class="layadmin-user-login-main">
            <div class="layadmin-user-login-box layadmin-user-login-header">
                <h2>{{ trans('login.header.head') }}</h2>
            </div>
            <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                    <input type="text" name="email" id="LAY-user-login-username"  lay-verify="email" placeholder="{{ trans('login.placeholder.email') }}" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                    <input type="password" name="password" id="LAY-user-login-password" placeholder="{{ trans('login.placeholder.password') }}" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="login-submit" id="login-submit">{{ trans('login.button.login') }}</button>
                </div>
            </div>
        </div>




    </div>


@endsection

@section('footerScripts')
    @parent
    <script>
        layui.config({
            base: "{{ url('plugin/layui/lay/modules/admin') }}/"      //自定义layui组件的目录
        }).extend({ //设定组件别名
            common:   'common',
            loadBar:   'loadBar',
        }).use(['layer', 'form' , 'jquery' , 'common' , 'loadBar'], function(){
            var layer = layui.layer,
                form = layui.form,
                $ = layui.$,
                loadBar = layui.loadBar,
                common = layui.common;
            form.verify({
                email: function(value, item){ //value：表单的值、item：表单的DOM对象
                    if(!new RegExp("^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$").test(value)){
                        return "{{ trans('login.verification.email') }}";
                    }
                }
                //我们既支持上述函数式的方式，也支持下述数组的形式
                //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
                ,pass: [
                    /^[\S]{6,12}$/
                    ,"{{ trans('login.verification.email') }}"
                ]
            });


            form.on('submit(login-submit)', function(data){
                return login(data);
            });

            $(document).keyup(function(event){
                if(event.keyCode ==13){
                    $('#login-submit').click();
                }
            });

            var login = function(data){
                loadBar.start();
                try{
                    common.ajax("{{ url('/backstage/login') }}", data.field, function (res) {
                        loadBar.finish();
                        common.prompt(res.message , 1 , 1500 , 6 , 'b' ,function () {
                            location.href="{{$redirect}}";
                        });
                    } , 'post' , function (e,xhr,opt) {
                        var msg = 'Unknown error';
                        if((opt=='Unprocessable Entity'&&e.status==422)||e.status==423)
                        {
                            var res = e.responseJSON;
                            msg = res.errors.email[0];
                        }else if(e.status==302)
                        {
                            var res = e.responseJSON;
                            msg = res.message;
                        }else if(e.status==419)
                        {
                            msg = 'The page has expired, please refresh the page and try again!';
                        }
                        console.log(e);
                        console.log(xhr);
                        console.log(opt);
                        common.prompt(msg , 5 , 1000 , 6 , 'auto' ,function () {
                            loadBar.error();
                        });

                    });
                }catch (e) {
                    loadBar.error();
                    console.error("Error name: " + e.name + "");
                    console.error("Error message: " + e.message);
                }
                return false;
            }
        });
    </script>
@endsection
