@foreach($all_menu as $menu)
    <li class="layui-nav-item @if(in_array($menu->menu_id , $menu_path)) layui-nav-itemed @endif" >
        @if(isset($menu->child))
            <a  href="javascript:;">{{$menu->menu_format_name}}</a>
            <dl class="layui-nav-child">
                @foreach($menu->child as $sub_menu)
                    <dd class="@if(in_array($sub_menu->menu_id , $menu_path))layui-nav-itemed @if(!isset($sub_menu->child)) layui-this @endif @endif">
                        @if(isset($sub_menu->child))
                            <a  href="javascript:;">{{$sub_menu->menu_format_name}}</a>
                            @include('layouts.child_side' , ['all_menu' => $sub_menu->child])
                        @else
                            @if(Auth::user()->can($sub_menu->menu_auth))
                                <a  href="{{url(App::getLocale().$sub_menu->menu_url)}}" >{{$sub_menu->menu_format_name}}</a>
                            @endif
                        @endif
                    </dd>
                @endforeach
            </dl>
        @else
            @if(Auth::user()->can($menu->menu_auth))
                <a  href="{{ url(App::getLocale().$menu->menu_url)}}" >{{$menu->menu_format_name}}</a>
            @endif
        @endif
    </li>
@endforeach



