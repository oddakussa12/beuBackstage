@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="disabled"><span>&raquo;</span></li>
        @endif
    </ul>
@endif

<style>
    .pagination { display: inline-block; padding-left: 0; margin: 20px 0; border-radius: 4px; }
    .pagination li { display: inline; }
    .pagination li a { position: relative; float: left; padding: 6px 12px; margin-left: -1px; line-height: 1.428571429; text-decoration: none; background-color: #fff; border: 1px solid #ddd; }
    .pagination li span { position: relative; float: left; padding: 6px 12px; margin-left: -1px; line-height: 1.428571429; text-decoration: none; background-color: #fff; border: 1px solid #ddd; }
    .pagination li:first-child a { margin-left: 0; border-bottom-left-radius: 4px; border-top-left-radius: 4px; }
    .pagination li:last-child a { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
    .pagination li a:hover, .pagination li a:focus { background-color: #eee; }
    .pagination .active a, .pagination .active a:hover, .pagination .active a:focus { z-index: 2; color: #fff; cursor: default; background-color: #009688; border-color: #009688; }
    .pagination .active span, .pagination .active span:hover, .pagination .active span:focus { z-index: 2; color: #fff; cursor: default; background-color: #009688; border-color: #009688; }
    .pagination .disabled a, .pagination .disabled a:hover, .pagination .disabled a:focus { color: #999; cursor: not-allowed; background-color: #fff; border-color: #ddd; }
    .pagination .disabled span, .pagination .disabled span:hover, .pagination .disabled span:focus { color: #999; cursor: not-allowed; background-color: #fff; border-color: #ddd; }
    .pagination-lg li a { padding: 10px 16px; font-size: 18px; }
    .pagination-sm li a, .pagination-sm li span { padding: 5px 10px; font-size: 12px; }
</style>
