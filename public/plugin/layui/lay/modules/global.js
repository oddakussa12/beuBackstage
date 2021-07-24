layui.define(['code', 'element', 'table', 'util'], function(exports){
    var $ = layui.jquery;
    //手机设备的简单适配
    var treeMobile = $('.site-tree-mobile')
        ,shadeMobile = $('.site-mobile-shade')
    if(window.sessionStorage.getItem('menu')=='on')
    {
        $('body').addClass('site-mobile');
    }
    treeMobile.on('click', function(){
        window.sessionStorage.setItem('menu' , 'on');
        $('body').addClass('site-mobile');
    });

    shadeMobile.on('click', function(){
        window.sessionStorage.setItem('menu' , 'off');
        $('body').removeClass('site-mobile');
    });

});