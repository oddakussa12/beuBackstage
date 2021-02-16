@foreach($child_menu as $sub_menu)
    <option id="menu_class_id_{{$menu->menu_id}}"  value="{{$sub_menu->menu_id}}"  layui-level="{{$sub_menu->menu_level}}" layui-path="{{$sub_menu->menu_path}}">{{$sub_menu->menu_format_name}}</option>
    @isset($sub_menu->child)
        @include('backstage.menu.child_menu_name' , ['child_menu'=>$sub_menu->child]);
    @endisset
@endforeach