/*
* @Author: sun(slf02@ourstu.com)
* @Date:   2018-09-14 10:00:00
*/
layui.extend({
    moment: '/lay/modules/admin/moment'
}).define(['laydate','jquery','moment'],function (exports) {
    "use strict";


    var MOD_NAME = 'timePicker',
        $ = layui.jquery,
        laydate = layui.laydate,
        moment = layui.moment;
    var timePicker = function () {
        this.v = '0.0.1';
    };

    /**
     * 初始化时间选择器
     */
    timePicker.prototype.render = function (opt) {

        var elem = $(opt.elem);

        //默认设置
        var locale = opt.options.locale || 'zh-CN';
        var timeStamp = opt.options.timeStamp || false;
        var format = opt.options.format || 'YYYY-MM-DD HH:mm:ss';
        if(locale=='zh-CN')
        {
            var languages = {
                'list': [
                    {
                        'class':'time-day',
                        'column':'天',
                        'first':{
                            'value':'1',
                            'txt':'今天',
                        },
                        'second':{
                            'value':'2',
                            'txt':'昨天',
                        },
                        'third':{
                            'value':'3',
                            'txt':'前天',
                        },
                    },
                    {
                        'class':'time-week',
                        'column':'周',
                        'first':{
                            'value':'4',
                            'txt':'本周',
                        },
                        'second':{
                            'value':'5',
                            'txt':'上周',
                        },
                        'third':{
                            'value':'6',
                            'txt':'下周',
                        },
                    },
                    {
                        'class':'time-month',
                        'column':'月',
                        'first':{
                            'value':'7',
                            'txt':'本月',
                        },
                        'second':{
                            'value':'8',
                            'txt':'上月',
                        },
                        'third':{
                            'value':'9',
                            'txt':'下月',
                        },
                    },
                    {
                        'class':'time-quarter',
                        'column':'季',
                        'first':{
                            'value':'10',
                            'txt':'本季度',
                        },
                        'second':{
                            'value':'11',
                            'txt':'上季度',
                        },
                        'third':{
                            'value':'12',
                            'txt':'下季度',
                        },
                    },
                    {
                        'class':'time-year',
                        'column':'年',
                        'first':{
                            'value':'13',
                            'txt':'本年度',
                        },
                        'second':{
                            'value':'14',
                            'txt':'上年度',
                        },
                        'third':{
                            'value':'15',
                            'txt':'下年度',
                        },
                    }
                ],
                'customize':'自定义',
                'yes':'确定',
                'no':'清除'
            }
        }else{
            var languages = {
                'list':[
                    {
                        'class':'time-day',
                        'column':'Day',
                        'first':{
                            'value':'1',
                            'txt':'Today',
                        },
                        'second':{
                            'value':'2',
                            'txt':'Yesterday',
                        },
                        'third':{
                            'value':'3',
                            'txt':'Dby',
                        },
                    },
                    {
                        'class':'time-week',
                        'column':'Week',
                        'first':{
                            'value':'4',
                            'txt':'This',
                        },
                        'second':{
                            'value':'5',
                            'txt':'Last',
                        },
                        'third':{
                            'value':'6',
                            'txt':'Next',
                        },
                    },
                    {
                        'class':'time-month',
                        'column':'Month',
                        'first':{
                            'value':'7',
                            'txt':'This',
                        },
                        'second':{
                            'value':'8',
                            'txt':'Last',
                        },
                        'third':{
                            'value':'9',
                            'txt':'Next',
                        },
                    },
                    {
                        'class':'time-quarter',
                        'column':'Quarter',
                        'first':{
                            'value':'10',
                            'txt':'This',
                        },
                        'second':{
                            'value':'11',
                            'txt':'Last',
                        },
                        'third':{
                            'value':'12',
                            'txt':'Next',
                        },
                    },
                    {
                        'class':'time-year',
                        'column':'Year',
                        'first':{
                            'value':'13',
                            'txt':'This',
                        },
                        'second':{
                            'value':'14',
                            'txt':'Last',
                        },
                        'third':{
                            'value':'15',
                            'txt':'Next',
                        },
                    }
                ],
                'customize':'Customize',
                'yes':'Yes',
                'no':'clear'
            }

        }
        elem.on('click',function (e) {
            e.stopPropagation();

            if($('.timePicker').length >= 1){
                $('.timePicker').remove();
                return false;
            }
            var t = elem.offset().top + elem.outerHeight()+"px";
            var l = elem.offset().left +"px";

            var timeDiv = '<div class="timePicker layui-anim layui-anim-upbit" style="left:'+l+';top:'+t+';">';
                timeDiv +='<div class="time-div">' +
                    '<div class="time-info">';
                var str = '';
                for(var l in languages.list){//遍历json数组时，这么写p为索引，0,1
                    console.log(l);
                    str += '<ul class="'+languages.list[l].class+'"><span>'+languages.list[l].column+'</span><li><input type="radio" name="day" value="'+languages.list[l].first.value+'">'+languages.list[l].first.txt+'</li><li><input type="radio" name="day" value="'+languages.list[l].second.value+'">'+languages.list[l].second.txt+'</li><li><input type="radio" name="day" value="'+languages.list[l].third.value+'">'+languages.list[l].third.txt+'</li></ul> ';
                }
                timeDiv += str;
                timeDiv += '</div>' +
                    '<div class="time-custom">' +
                    '<div class="layui-timepicker-custom"  data-role="display">' +
                    '<span>'+languages.customize+'</span>' +
                    '<i class="layui-icon layui-icon-down" ></i>' +
                    '</div> ' +
                    '<div class="time-select">' +
                    '<input type="text" class="layui-input" id="sTime">' +
                    '<input type="text" class="layui-input" id="eTime">' +
                    '</div></div>' +
                    '<div class="time-down">' +
                    '<div class="sure" data-role="sure">'+languages.yes+'</div>' +
                    '<div class="no" data-role="no">'+languages.no+'</div>' +
                    '</div>' +
                    '</div>';
                timeDiv = $(timeDiv);
            //渲染
            $('body').append(timeDiv);
            //自定义时间显示
            $('[data-role="display"]').on('click',function () {
                var current = elem.val();
                console.log(current);
                if(current!='')
                {
                    current = current.split(' - ');
                    var start = current[0];
                    var end = current[1];
                    $('#sTime').val(start);
                    $('#eTime').val(end);
                }else{
                    current = getStartEndTime('1');
                    var start = current[0];
                    var end = current[1];
                    $('#sTime').val(start);
                    $('#eTime').val(end);
                }
                $('.time-select').css('display','flex');
                $(this).find('i').remove();
                // var sTime = $('#sTime').val();
            });
            //自定义时间选择器
            laydate.render({elem: '#sTime' , type: 'datetime' , lang: locale});
            laydate.render({elem: '#eTime' , type: 'datetime' , lang: locale});

            //选择固定日期
            var $li=$('.time-info').children().find('li');
            $li.on('click',function () {
                $('.time-info').children().find('li').removeClass('active');
               if($(this).children('input').is(':checked')){
                   $(this).children('input').prop('checked',false);
               }else{
                   $(this).addClass('active');
                   $(this).children().prop('checked',true);
                   var startEnd = getStartEndTime($('.time-info').children().find('input:checked').val());
                    $("#sTime").val(startEnd[0]);
                    $("#eTime").val(startEnd[1]);
               }

            });
            //确定后生成时间区间 如：2018-9-14 - 2018-9-15
            $('[data-role="no"]').on('click',function () {
                elem.val('');
                $('.timePicker').remove();
            });
            $('[data-role="sure"]').on('click',function () {
                var inputVal=$('.time-info').children().find('input:checked').val();
                var sTime='';
                var eTime='';
                switch (inputVal){
                    case '1'://今天
                        sTime=moment.moment().startOf('day');
                        eTime=moment.moment().endOf('day');
                        break;
                    case '2'://昨天
                        sTime=moment.moment().subtract(1, 'days').startOf('day');
                        eTime=moment.moment().subtract(1, 'days').endOf('day');
                        break;
                    case '3'://前天
                        sTime=moment.moment().subtract(2, 'days').startOf('day');
                        eTime=moment.moment().subtract(2, 'days').endOf('day');
                        break;
                    case '4'://本周
                        sTime=moment.moment().startOf('week');
                        eTime=moment.moment().endOf('week');
                        break;
                    case '5'://上周
                        sTime=moment.moment().subtract(1,'week').startOf('week');
                        eTime=moment.moment().subtract(1,'week').endOf('week');
                        break;
                    case '6'://下周
                        sTime=moment.moment().subtract(-1,'week').startOf('week');
                        eTime=moment.moment().subtract(-1,'week').endOf('week');
                        break;
                    case '7'://本月
                        sTime=moment.moment().startOf('month');
                        eTime=moment.moment().endOf('month');
                        break;
                    case '8'://上月
                        sTime=moment.moment().subtract(1,'month').startOf('month');
                        eTime=moment.moment().subtract(1,'month').endOf('month');
                        break;
                    case '9'://下月
                        sTime=moment.moment().subtract(-1,'month').startOf('month');
                        eTime=moment.moment().subtract(-1,'month').endOf('month');
                        break;
                    case '10'://本季度
                        sTime=moment.moment().startOf('quarter');
                        eTime=moment.moment().endOf('quarter');
                        break;
                    case '11'://上季度
                        sTime=moment.moment().subtract(1,'quarter').startOf('quarter');
                        eTime=moment.moment().subtract(1,'quarter').endOf('quarter');
                        break;
                    case '12'://下季度
                        sTime=moment.moment().subtract(-1,'quarter').startOf('quarter');
                        eTime=moment.moment().subtract(-1,'quarter').endOf('quarter');
                        break;
                    case '13'://本年度
                        sTime=moment.moment().startOf('year');
                        eTime=moment.moment().endOf('year');
                        break;
                    case '14'://上年度
                        sTime=moment.moment().subtract(1,'year').startOf('year');
                        eTime=moment.moment().subtract(1,'year').endOf('year');
                        break;
                    case '15'://下年度
                        sTime=moment.moment().subtract(-1,'year').startOf('year');
                        eTime=moment.moment().subtract(-1,'year').endOf('year');
                        break;
                    default:
                        sTime=$('#sTime').val();
                        eTime=$('#eTime').val();
                        break;
                }
                var timeDate='';
                if(inputVal){
                    if(timeStamp){
                        timeDate =parseInt(sTime/1000) + ' - ' + parseInt(eTime/1000);
                    }else{
                        timeDate = sTime.format(format) + ' - ' + eTime.format(format);
                    }
                }else{
                    timeDate=sTime + ' - ' + eTime;
                }
                elem.val(timeDate);
                $('.timePicker').remove();
            });
            const getStartEndTime = function (inputVal) {
                var sTime,eTime;
                switch (inputVal) {
                    case '1'://今天
                        sTime = moment.moment().startOf('day');
                        eTime = moment.moment().endOf('day');
                        break;
                    case '2'://昨天
                        sTime = moment.moment().subtract(1, 'days').startOf('day');
                        eTime = moment.moment().subtract(1, 'days').endOf('day');
                        break;
                    case '3'://前天
                        sTime = moment.moment().subtract(2, 'days').startOf('day');
                        eTime = moment.moment().subtract(2, 'days').endOf('day');
                        break;
                    case '4'://本周
                        sTime = moment.moment().startOf('week');
                        eTime = moment.moment().endOf('week');
                        break;
                    case '5'://上周
                        sTime = moment.moment().subtract(1, 'week').startOf('week');
                        eTime = moment.moment().subtract(1, 'week').endOf('week');
                        break;
                    case '6'://下周
                        sTime = moment.moment().subtract(-1, 'week').startOf('week');
                        eTime = moment.moment().subtract(-1, 'week').endOf('week');
                        break;
                    case '7'://本月
                        sTime = moment.moment().startOf('month');
                        eTime = moment.moment().endOf('month');
                        break;
                    case '8'://上月
                        sTime = moment.moment().subtract(1, 'month').startOf('month');
                        eTime = moment.moment().subtract(1, 'month').endOf('month');
                        break;
                    case '9'://下月
                        sTime = moment.moment().subtract(-1, 'month').startOf('month');
                        eTime = moment.moment().subtract(-1, 'month').endOf('month');
                        break;
                    case '10'://本季度
                        sTime = moment.moment().startOf('quarter');
                        eTime = moment.moment().endOf('quarter');
                        break;
                    case '11'://上季度
                        sTime = moment.moment().subtract(1, 'quarter').startOf('quarter');
                        eTime = moment.moment().subtract(1, 'quarter').endOf('quarter');
                        break;
                    case '12'://下季度
                        sTime = moment.moment().subtract(-1, 'quarter').startOf('quarter');
                        eTime = moment.moment().subtract(-1, 'quarter').endOf('quarter');
                        break;
                    case '13'://本年度
                        sTime = moment.moment().startOf('year');
                        eTime = moment.moment().endOf('year');
                        break;
                    case '14'://上年度
                        sTime = moment.moment().subtract(1, 'year').startOf('year');
                        eTime = moment.moment().subtract(1, 'year').endOf('year');
                        break;
                    case '15'://下年度
                        sTime = moment.moment().subtract(-1, 'year').startOf('year');
                        eTime = moment.moment().subtract(-1, 'year').endOf('year');
                        break;
                    default:
                        eTime = sTime = moment.moment();
                        break;
                }
                return [sTime.format(format), eTime.format(format)]
            };
        })
    };

    /**
     * 隐藏选择器
     */
    timePicker.prototype.hide = function (opt) {
        $('.timePicker').remove();
    };

    //自动完成渲染
    var timePicker = new timePicker();

    //FIX 滚动时错位
    $(window).scroll(function () {
        timePicker.hide();
    });

    exports(MOD_NAME, timePicker);
}).link('/plugin/layui/style/timepicker.css');