@foreach($child_menu as $sub_menu)
    <tr>
        <td>{{$sub_menu->menu_id}}</td>
        <td>{{$sub_menu->menu_p_id}}</td>
        <td>
                        <span class="treeTable-icon open" lay-tid="{{$sub_menu->menu_id}}" lay-tpid="{{$sub_menu->menu_p_id}}"  @if($sub_menu->child) lay-ttype="dir" @else lay-ttype="file" @endif>
                            @for($i=0;$i<=$sub_menu->menu_level;$i++)
                                <span class="treeTable-empty"></span>
                            @endfor
                            @if($sub_menu->child)
                                <i class="layui-icon layui-icon-triangle-d"></i>
                                <i class="layui-icon layui-icon-layer"></i>
                            @else
                                <i class="layui-icon layui-icon-file"></i>
                            @endif
                            &nbsp;&nbsp;{{$sub_menu->menu_name}}
                        </span>
        </td>
        @if(count($supportedLocales)>=1)
            @foreach($supportedLocales as $localeCode => $properties)
                @php
                    $flag = false;
                @endphp
                @foreach($sub_menu->translations as $translation)
                    @if($translation->menu_locale==$localeCode)
                        @php
                            $flag = true;
                        @endphp
                        <td>{{$translation->menu_name}}</td>
                    @endif
                @endforeach
                @if($flag===false)
                    <td></td>
                @endif
            @endforeach
        @endif
        <td>{{$sub_menu->menu_auth}}</td>
        <td>{{$sub_menu->menu_url}}</td>
        <td>
            {{--<div class="layui-table-cell laytable-cell-1-6">--}}
                {{--<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>--}}
                {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>--}}
            {{--</div>--}}
        </td>

    </tr>
    @isset($sub_menu->child)
        @include('backstage.menu.child_menu' , ['child_menu'=>$sub_menu->child])
    @endisset
@endforeach