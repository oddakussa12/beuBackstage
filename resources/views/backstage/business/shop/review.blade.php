@extends('layouts.dashboard')
@section('layui-content')
    <style>
        .select {position: absolute;top: -19px;right: -6vw;}
        table td { height: 40px; line-height: 40px;}
        .layui-form-label {padding: 9px 9px 5px 0; text-align: left;}
    </style>
    <div  class="layui-fluid">
        <form class="layui-form">
            <div class="layui-inline">
                <label class="layui-form-label">{{trans('business.table.header.shop_name')}}:</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="keyword" placeholder="{{trans('business.table.header.shop_name')}}" id="keyword" @if(!empty($keyword)) value="{{$keyword}}" @endif/>
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn select" type="submit"  lay-submit >{{trans('common.form.button.submit')}}</button>
            </div>
        </form>
        <table class="layui-table" lay-filter="table" id="table">
            @if(!empty($result))
            <tr>
                <td>{{trans('user.table.header.phone')}} : {{$result->user_phone_country}} {{$result->user_phone}}</td>
            </tr>
            <tr>
                <td>{{trans('business.table.header.shop_name')}} : {{$result->user_name}}</td>
            </tr>
            <tr>
                <td>{{trans('business.table.header.shop_nick_name')}} : {{$result->user_nick_name}}</td>
            </tr>
            <tr>
                <td>{{trans('common.table.header.status')}} :
                    <span class="layui-btn layui-btn-xs @if($result->user_verified==-1) layui-btn-danger @elseif($result->user_verified==0) layui-btn-warm @else layui-btn-normal @endif">
                        @if($result->user_verified==1) Pass @elseif($result->user_verified==0) Refuse @else UnAudited @endif
                    </span>
                </td>
            </tr>
            <tr>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    @if(!empty($result->user_id))
                        <input type="hidden" id="id" name="id" value="{{$result->user_id}}">
                        <button class="layui-btn layui-btn-normal submit   @if($result->user_verified==1) layui-btn-disabled @endif" value="yes">Pass</button>
                        <button class="layui-btn layui-btn-warm submit @if($result->user_verified==0) layui-btn-disabled @endif" value="no">Refuse</button>
                    @endif
                </td>
            </tr>
            @else
                <tr>
                    <td>
                        Not Found. Please check the input and make sure it's a store
                    </td>
                </tr>
            @endif
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
        }).use(['common' , 'table' , 'layer' , 'timePicker'], function () {
            const form = layui.form,
                common = layui.common,
                $ = layui.jquery;
            $(".submit").click(function () {

                @if(!Auth::user()->can('business::shop.update'))
                common.tips("{{trans('common.ajax.result.prompt.no_permission')}}");
                form.render();
                return false;
                @endif;
                let val    = this.value;
                let params = '{"user_verified":"'+val+'"}';
                let id = $("#id").val();
                common.ajax("{{url('/backstage/business/shop')}}/"+id, JSON.parse(params) , function(res){
                    common.prompt("{{trans('common.ajax.result.prompt.update')}}" , 1 , 300 , 6 , 't');
                    location.reload();
                } , 'put');
            });
        });
    </script>
@endsection