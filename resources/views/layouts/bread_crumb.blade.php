@if(!empty($menu_path))
<fieldset class="layui-elem-field layui-field-title">
    <legend>
        <span class="layui-breadcrumb">
            @foreach($menu_path as $menu_id)
                @foreach($native_menus as $menu)
                    @if($menu_id==$menu['menu_id'])
                        <a href="javascript:;">{{isset($menu['menu_name'])?$menu['menu_name']:''}}</a>
                    @endif
                @endforeach
            @endforeach
        </span>
    </legend>
</fieldset>
@endif