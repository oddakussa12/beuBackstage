<div class="layui-logo" >{{trans('common.header.title')}}</div>

@php
    $supportedLocales = LaravelLocalization::getSupportedLocales();
@endphp
<ul class="layui-nav layui-layout-right">
    <li class="layui-nav-item">
        <a href="javascript:;">
            {{ Auth::user()->admin_email }}
        </a>
        <dl class="layui-nav-child">
            <dd><a href="{{url(App::getLocale().'/backstage/user') }}">{{ trans('common.button.personal_information') }}</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item">
        @if(count($supportedLocales)>=1)
            <a rel="alternate" lang="{{App::getLocale()}}" href="javascript:;">
                {!! $supportedLocales[App::getLocale()]['native'] !!}
            </a>

                <dl class="layui-nav-child">
                    @foreach($supportedLocales as $localeCode => $properties)
                    <dd>
                        <a rel="alternate" lang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                            {!! $properties['native'] !!}
                        </a>
                    </dd>
                    @endforeach
                </dl>
        @endif
    </li>
    <li class="layui-nav-item"><a href="{{ url(App::getLocale().'/backstage/logout') }}">{{ trans('common.button.sign_out') }}</a></li>
</ul>

