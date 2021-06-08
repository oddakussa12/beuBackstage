layui.define(["jquery","layer","table"],function(exports){
    var $ = layui.jquery;
    var layer = layui.layer;
    var table = layui.table;
    var checked = function (params) {
        if(!params){
            layer.msg('下拉列表参数为空');
            return false;
        }
        if(!params.layFilter || params.layFilter===''){
            layer.msg('表监听事件[lay-filter]为空');
            return false;
        }
        if(!params.event || params.event===''){
            layer.msg('列监听事件[event]为空');
            return false;
        }
        if((!params.data || params.data.length === 0) && (!params.url || params.url === '')){
            layer.msg('下拉列表数据或url为空');
            return false;
        }
        return true;
    };
    var deleteDl = function (td) {
        var isPass = true;
        $(td).parent().parent().find('dl').each(function (e) {
            if(this.parentNode===td){
                isPass = false;
            }
            $(this).parent().removeClass('layui-table-selected');
            $(this).remove();
        });
        return isPass;
    };
    var active = {
        callback:{},
        config:{params:{}}
        ,addSelect:function (params) {
            //非url模式 params:{data:[],layFilter:'',event:''}
            //url模式 params:{url:'',where:{},layFilter:'',event:'',parseData:function(){}} parseData为回调函数
            if(!checked(params)){return;}
            if(this.config.params[params.layFilter] && this.config.params[params.layFilter][params.event]){
                layer.msg('lay-filter='+params.layFilter+',lay-event='+event+'已被占用');
                return;
            }else {
                if(!this.config.params[params.layFilter]){
                    this.config.params[params.layFilter]={};
                }
                if(!params.data){
                    $.getJSON(params.url,params.where,function (result) {
                        if(params.parseData){
                            active.config.params[params.layFilter][params.event] = params.parseData(result.data);
                        }else {
                            active.config.params[params.layFilter][params.event] = result.data;
                        }
                    });
                }else {
                    this.config.params[params.layFilter][params.event] = params.data;
                }
            }
            if(params.callback)
            {
                this.callback =params.callback;
            }
            this.initSelect();
        }
        ,initSelect:function () {
            var othis = this;
            var params = othis.config.params;
            for(var key in params){
                table.on('tool('+key+')', function(obj){
                    var callback = othis.callback;
                    var thisSelectParams = params[key][obj.event];
                    if(!thisSelectParams){
                        return;
                    }
                    if(!deleteDl(this)){return;}
                    var that = this;
                    $(that).addClass('layui-table-selected');
                    var selectHtml = [];
                    selectHtml.push('<dl class="layui-table-select-dl">');
                    thisSelectParams.forEach(function (e) {
                        selectHtml.push('<dd lay-value="'+e.name+'" class="layui-table-select-dd">'+e.value+'</dd>');
                    });
                    selectHtml.push('</dl>');
                    $(that).append(selectHtml.join(" "));

                    $(that).find('dl dd').bind('click',function (e) {
                        $(that).find('dl dd').each(function () {
                            if($(this).hasClass('layui-this')){
                                $(this).removeClass('layui-this');
                            }
                        });
                        $(this).addClass('layui-this');
                        $(that).removeClass('layui-table-selected');
                        var update = {};
                        // console.log(2222);
                        // console.log($(that).attr('data-field'));
                        // console.log($(this).attr('lay-value'));
                        // console.log(22223);
                        update[$(that).attr('data-field')] = $(this).attr('lay-value');
                        $(that).find('dl').remove();
                        callback(obj,update)
                    });
                });
            }
        }
    };

    layui.link(layui.cache.base + 'lay/modules/admin/table-select/css/layui-table-select.css');

    exports('layuiTableColumnSelect', active);
});