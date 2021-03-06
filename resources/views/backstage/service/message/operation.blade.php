@extends('layouts.dashboard')
@section('layui-content')
    <div  class="layui-fluid">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>Message</legend>
        </fieldset>
        <form class="layui-form"  method="post">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Send Type</label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <select name="type" lay-verify="required"  multiple="" lay-filter="type">
                            <option value="0" selected>All</option>
                            <option value="1">Country</option>
                            <option value="2">Private</option>
                            <option value="3">Friend</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline"  style="display: none;" id="countryDiv" >
                    <label class="layui-form-label">Country</label>
                    <div class="layui-input-inline">
                        <select name="country" lay-verify="required" xm-select="country" lay-filter="country" id="country">
                            <option value="tl" selected>East Timor</option>
                            <option value="id">Indonesia</option>
                            <option value="gl">Grenada</option>
                            <option value="au">Australia</option>
                            <option value="et">Ethiopia</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline" >
                    <label class="layui-form-label">From</label>
                    <div class="layui-input-inline">
                        <input type="text" name="sender" lay-verify="required" autocomplete="off" class="layui-input" id="userId" placeholder="861388888888">
                    </div>
                </div>
                <div class="layui-inline" id="targetIdDiv" style="display: none;" >
                    <label class="layui-form-label">To</label>
                    <div class="layui-input-inline">
                        <input type="text" name="target" autocomplete="off" class="layui-input" id="targetId" disabled="disabled"  placeholder="861388888888">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-upload">
                        <button type="button" class="layui-btn" id="image"><i class="layui-icon">???</i>Upload Image</button>
                        <button type="button" class="layui-btn" id="video"><i class="layui-icon">???</i>Upload Video</button>
                        <button class="layui-btn" lay-submit="">Send</button>
                        <div class="layui-upload-list">
                            <table class="layui-table">
                                <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Operate</th>
                                </tr>
                                </thead>
                                <tbody id="fileList">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-form-item" id="media">

            </div>
        </form>
    </div>
@endsection
@section('footerScripts')
    @parent
    <script>
        //??????????????????
        layui.config({
            base: "{{url('plugin/layui')}}/"
        }).extend({
            common: 'lay/modules/admin/common',
            loadBar: 'lay/modules/admin/loadBar',
            formSelects: 'lay/modules/formSelects-v4',
        }).use(['common' , 'table' , 'layer' , 'carousel' , 'element' , 'upload' , 'loadBar' , 'formSelects'], function () {
            var form = layui.form,
                layer = layui.layer,
                table = layui.table,
                loadBar = layui.loadBar,
                common = layui.common,
                carousel = layui.carousel,
                $=layui.jquery,
                formSelects=layui.formSelects,
                upload = layui.upload;
                formSelects.render('country');

                var image = upload.render({
                    elem: '#image'
                    ,accept: 'images' //??????
                    ,auto: false
                    ,choose:function (obj , test){
                        var formData = new FormData();
                        var files = obj.pushFile();
                        // console.log(files);
                        var keys = Object.keys(files);
                        var end = keys[keys.length-1]
                        var file = files[end];
                        var formData = new FormData();

                        common.ajax("{{config('common.lovbee_domain')}}api/aws/image/form?file="+file.name , {} , function(res){
                            image.config.url = res.action;
                            for (p in res.form) {
                                formData.append(p, res.form[p]);
                            }
                            // image.config.data = res.form;
                            // image.config.headers = {
                            //     'Content-Type': false,
                            // };
                            formData.append('file',file);
                            $.ajax({
                                url:res.action,
                                type:'POST',
                                data: formData,
                                processData:false,
                                cache:false,
                                contentType:false,
                                xhr:jqureAjaxXhrOnProgress(function(e){
                                    var percent=e.loaded / e.total;
                                    percent = Math.round((percent + Number.EPSILON) * 100);
                                    $("#upload_progress").html(percent);
                                }),
                                beforeSend: function(obj){ //obj??????????????????????????? choose???????????????????????????????????????
                                    common.prompt('uploaded<span id="upload_progress"></span>%' , 16 , 0 , 0 , 'auto' , undefined , [0.8, '#393D49']);
                                },
                                success:function(data){
                                    console.log(res.domain+res.form.key)
                                    var fileListView = $('#fileList')
                                    fileListView.find('.image-tr').remove();
                                    var tr = $(['<tr class="image-tr">'
                                        ,'<td><a href="'+res.domain+res.form.key+'" target="_blank">'+ res.domain+res.form.key +'</a><input type="hidden" name="image" value="'+res.domain+res.form.key+'"></td>'
                                        ,'</td>'
                                        ,'<td><button type="button" class="layui-btn layui-btn-xs layui-btn-danger file-delete">delete</button>'
                                        ,'</td>'
                                        ,'</tr>'].join(''));
                                    //??????
                                    tr.find('.file-delete').on('click', function(obj){
                                        // console.log($(obj.target).parent());
                                        // return;
                                        // var row = $(obj.target).parent().parent();
                                        $(obj.target).closest('tr').remove();
                                        // var tb = row.parent();   //??????????????????
                                        // var rowIndex = row.rowIndex;	//????????????????????????
                                        // tb.deleteRow(rowIndex);  //????????????????????????
                                        table.render();
                                    });
                                    fileListView.append(tr);
                                    // $('#media').html(
                                    //     "<div class='layui-inline'>"+
                                    //         "<div class='layui-input-inline'>"+
                                    //             "<input type='text' name='image' class='layui-input' value='"+res.domain+res.form.key+"'>"+
                                    //         "</div>"+
                                    //     "</div>"
                                    // );
                                },
                                error:function(){
                                    alert('upload failed');
                                },
                                complete:function (){
                                    layer.closeAll();
                                }
                            })
                        } , 'get' , undefined , undefined , false);
                    }
                });
            var jqureAjaxXhrOnProgress = function(fun) {
                jqureAjaxXhrOnProgress.onprogress = fun; //????????????
                //???????????????????????????
                return function() {
                    //??????$.ajaxSettings.xhr();??????XMLHttpRequest??????
                    var xhr = $.ajaxSettings.xhr();
                    //?????????????????????????????????
                    if (typeof jqureAjaxXhrOnProgress.onprogress !== 'function')
                        return xhr;
                    //???????????????????????????xhr???????????????????????????????????????????????????
                    if (jqureAjaxXhrOnProgress.onprogress && xhr.upload) {
                        xhr.upload.onprogress = jqureAjaxXhrOnProgress.onprogress;
                    }
                    return xhr;
                }
            }
            var video = upload.render({
                    elem: '#video'
                    ,accept: 'video' //??????
                    ,auto: false
                    ,choose:function (obj , test){
                        var formData = new FormData();
                        var files = obj.pushFile();
                        // console.log(files);
                        var keys = Object.keys(files);
                        var end = keys[keys.length-1]
                        var file = files[end];
                        var formData = new FormData();

                        common.ajax("{{config('common.lovbee_domain')}}api/aws/video/form?file="+file.name , {} , function(res){
                            // layer.closeAll();
                            image.config.url = res.action;
                            for (p in res.form) {
                                formData.append(p, res.form[p]);
                            }
                            // image.config.data = res.form;
                            // image.config.headers = {
                            //     'Content-Type': false,
                            // };
                            formData.append('file',file);
                            $.ajax({
                                url:res.action,
                                type:'POST',
                                data: formData,
                                processData:false,
                                cache:false,
                                contentType:false,
                                xhr:jqureAjaxXhrOnProgress(function(e){
                                    var percent=e.loaded / e.total;
                                    percent = Math.round((percent + Number.EPSILON) * 100);
                                    $("#upload_progress").html(percent);
                                }),
                                beforeSend: function(obj){ //obj??????????????????????????? choose???????????????????????????????????????
                                    layer.closeAll();
                                    common.prompt('uploaded<span id="upload_progress"></span>%' , 16 , 0 , 0 , 'auto' , undefined , [0.8, '#393D49']);
                                },
                                success:function(data){
                                    console.log(res.domain+res.form.key)
                                    var fileListView = $('#fileList')
                                    fileListView.find('.video-tr').remove();
                                    var tr = $(['<tr class="video-tr">'
                                        ,'<td><a href="'+res.domain+res.form.key+'" target="_blank">'+ res.domain+res.form.key +'</a><input type="hidden" name="video" value="'+res.domain+res.form.key+'"></td>'
                                        ,'</td>'
                                        ,'<td><button type="button" class="layui-btn layui-btn-xs layui-btn-danger file-delete">delete</button>'
                                        ,'</td>'
                                        ,'</tr>'].join(''));
                                    tr.find('.file-delete').on('click', function(obj){
                                        // console.log($(obj.target).parent());
                                        // return;
                                        // var row = $(obj.target).parent().parent();
                                        $(obj.target).closest('tr').remove();
                                        // var tb = row.parent();   //??????????????????
                                        // var rowIndex = row.rowIndex;	//????????????????????????
                                        // tb.deleteRow(rowIndex);  //????????????????????????
                                        table.render();
                                    });
                                    fileListView.append(tr);
                                    // $('#media').html(
                                    //     "<div class='layui-inline'>"+
                                    //         "<div class='layui-input-inline'>"+
                                    //             "<input type='text' name='image' class='layui-input' value='"+res.domain+res.form.key+"'>"+
                                    //         "</div>"+
                                    //     "</div>"
                                    // );
                                },
                                error:function(){
                                    alert('upload failed');
                                },
                                complete:function (){
                                    console.log('finish');
                                    layer.closeAll();
                                }
                            })
                        } , 'get' , undefined , undefined , false);
                    }
                });
                form.on('select(type)', function (data) {
                    console.log(data);
                    if (data.value == 0||data.value == 3) {
                        $("#targetId").attr("disabled", "true");
                        $("#targetIdDiv").hide();
                        $("#country").attr("disabled", "true");
                        $("#targetId").removeAttr("lay-verify");
                        $("#countryDiv").hide();
                        form.render('select');
                    }else if (data.value == 1) {
                        $("#targetId").attr("disabled", "true");
                        $("#targetIdDiv").hide();
                        $("#country").removeAttr("disabled");
                        $("#targetId").removeAttr("lay-verify");
                        $("#countryDiv").show();
                        form.render('select');//select??????????????? ???????????????
                    } else {
                        $("#targetId").removeAttr("disabled");
                        $("#targetIdDiv").show();
                        $("#country").attr("disabled", "true");
                        $("#targetId").attr("lay-verify", "required");
                        $("#countryDiv").hide();
                        form.render('select');

                    }
                });
        });
    </script>

@endsection