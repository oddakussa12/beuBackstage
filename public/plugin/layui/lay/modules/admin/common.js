layui.define(['jquery'], function(exports){
    var $ = layui.jquery;
    var call_back = function(index, layero){
        //按钮【按钮二】的回调
        layer.close(index);
        return false; //开启该代码可禁止点击该按钮关闭
    };
    var obj = {
        ajax: function (url, data, callback , type, error, dataType , async) {
            url = url==undefined?'':url;
            type = type==undefined?'post':type;
            data = data==undefined?{_time:time()}:data;
            dataType = dataType==undefined?'json':dataType;
            callback = callback==undefined?this.init_success:callback;
            error = error==undefined?this.init_error:error;
            async = async==undefined?true:async;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: type,
                async:async,
                beforeSend:function(){
                    layer.closeAll();
                    layer.load(2, {
                        shade: [0.8,'#393D49'] //0.1透明度的白色背景
                    });
                },
                dataType: dataType,
                data: data,
                success: callback,
                error: error,
                complete:function(){
                    // layer.closeAll();
                }
            });
        },
        init_success:function(data){
            data = data==undefined?'init success':data;
            console.log(data);return false;
        },
        init_error:function (event,xhr,options,exc) {
            console.log("event:"+event);
            console.log("xhr:"+xhr);
            console.log("options:"+options);
            console.log("exc:"+exc);
            var readyState = event.readyState;
            if(readyState===4)
            {
                var status = event.status;
                switch (status) {
                    case 422:
                        var message = event.responseJSON.message;
                        var errors = event.responseJSON.errors;
                        var str = 'message：<br />'+message+'<br />errors：<br />';
                        $.each(errors , function (k , v) {
                            str+=k+':'
                            $.each(v , function (i , j) {
                                str+=j+'；';
                            })
                            str+='<br />';
                        });
                        obj.prompt(str , 5  ,2000 , 1 , 't');
                        break;
                    case 401:
                        var message = event.responseJSON.message;
                        obj.prompt(message , 5  ,500 , 1 , 't' , function () {
                            window.location.reload();
                        });
                        break;
                    case 403:
                        var message = event.responseJSON.message;
                        obj.prompt(message , 5  ,2000 , 1 , 't');
                        break;
                    default:
                        alert("Error Message:"+event.responseJSON.message+"\nError Code:"+event.status+"\nError Result:"+xhr+"\nError Reason:"+options);
                        break;
                }


            }else{
                var msg = '';
                switch (event.readyState) {
                    case 0:
                        msg = '初始化，XMLHttpRequest对象还没有完成初始化';
                        break;
                    case 1:
                        msg = '载入，XMLHttpRequest对象开始发送请求';
                        break;
                    case 2:
                        msg = '载入完成，XMLHttpRequest对象的请求发送完成';
                        break;
                    case 3:
                        msg = '解析，XMLHttpRequest对象开始读取服务器的响应';
                        break;
                    default:
                        msg = '未知状态码：'+event.readyState;
                        break;
                }
                console.error(msg);
                alert("Error readyState:"+event.readyState+"\nError Code:"+event.status+"\nError Result:"+xhr+"\nError Reason:"+event.responseText);
            }
        },
        prompt:function (msg , icon , time , shift , offset ,callback , shade) {
            if(msg==undefined||msg=='')
            {
                return false;
            }
            msg = msg.toString();
            icon = icon==undefined?1:parseInt(icon);
            shift = shift==undefined?1:parseInt(shift);
            time = time==undefined?1000:parseInt(time);
            offset = offset==undefined?'b':offset.toString();
            callback = callback==undefined?this.init_success:callback;
            shade = shade==undefined?0:shade;
            layer.closeAll();
            layer.msg(msg, {
                icon: icon,
                time: time,
                offset:offset,
                shift:shift,
                shade:shade
            } , callback);
        },
        tips:function (str , obj , params) {
            params = params == undefined?{time:400,tips: [2, '#FFB800']}:params;
            layer.tips(str , obj , params);
        },
        //type：
        // 0（信息框，默认）
        // 1（页面层）
        // 2（iframe层）
        // 3（加载层）
        // 4（tips层）。 若你采用layer.open({type: 1})方式调用，则type为必填项（信息框除外）
        open:function (content , other , type) {
            type = type==undefined?2:type;
            content = content==undefined?'':content;
            other = other==undefined?{}:other;
            var options = {
                id:(new Date()).getTime(),
                type: type,
                content: content //这里content是一个普通的String
            };
            options.resize = other.hasOwnProperty('resize')?other.resize:true;
            options.scrollbar = other.hasOwnProperty('scrollbar ')?other.scrollbar :true;
            options.title = other.hasOwnProperty('title')?other.title:false;
            options.anim = other.hasOwnProperty('anim')?other.anim:2;
            options.time = other.hasOwnProperty('time')?other.time:0;
            options.closeBtn = other.hasOwnProperty('closeBtn')?other.closeBtn:2;
            options.shadeClose = other.hasOwnProperty('shadeClose')?other.shadeClose:true;
            options.shade = other.hasOwnProperty('shade')?other.shade:0.8;
            options.area = other.hasOwnProperty('area')?other.area:'auto';
            options.maxmin = other.hasOwnProperty('maxmin')?other.maxmin:false;
            switch (type) {
                case 0:
                    break;
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    break;
                case 4:
                    break;
                default:
                    break;
            }
            console.log(options);
            layer.open(options);
        },
        //类型：Number，默认：-1（信息框）/0（加载层）
        // 信息框默认不显示图标。当你想显示图标时，默认皮肤可以传入0-6如果是加载层，可以传入0-2。如：
        confirm:function (message , success , params , cancel) {
            var opations = {};
            params = typeof(params)=="undefined"?{}:params;
            message = typeof(message)=='string'?message:'Are you sure you want to continue?';
            opations.icon = typeof(params.icon)=="undefined"?3:params.icon;
            opations.btn = typeof(params.btn)!="undefined"?params.btn:['Yes','Cancel'];
            opations.title = typeof(params.title)=="undefined"?'Notice':params.title;
            success = typeof(success)!='function'?call_back:success;
            cancel = typeof(cancel)!='function'?call_back:cancel;
            layer.confirm(message, opations, success, cancel);
        },
        fieldToArr:function (field , supportedLocales) {
            var params = {};
            var lang = new Array();
            $.each(field , function (k ,v) {
                if(v===null||v===undefined)
                {
                    return true;
                }
                $.each(supportedLocales , function (_i ,_j) {
                    var locale = _i;
                    var i = k.indexOf('_');
                    if(k.substring(0 , i)==locale)
                    {
                        lang.push(k);
                        var field = k.substring(i+1);
                        if(params.hasOwnProperty(locale))
                        {
                            params[locale][field] = v;
                        }else{
                            params[locale] = {};
                            params[locale][field] = v;
                        }
                    }else{
                        params[k] = v;
                    }
                });
            });
            $.each(lang , function (k , v) {
                delete params[v];
            });
            return params;
        }

    };
    //输出接口
    exports('common', obj);
});