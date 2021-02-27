<dl class="layui-nav-child">
@foreach($all_menu as $menu)
        <dd  class="@if(in_array($menu->menu_id , $menu_path))layui-nav-itemed @if(!isset($menu->child)) layui-this @endif @endif">
            @if(isset($menu->child))
                <a  href="javascript:;">{{$menu->menu_format_name}}</a>
                @include('layouts.child_side' , ['all_menu' => $menu->child])
            @else
                @if(Auth::user()->can($menu->menu_auth))
                    <a  href="{{ url($menu->menu_url) }}">{{$menu->menu_format_name}}</a>
                @endif
            @endif
        </dd>
@endforeach
</dl>