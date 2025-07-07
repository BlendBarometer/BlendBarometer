@if ($paginator->hasPages())
<nav aria-label="Pagination">
  <ul class="pagination pagination-sm justify-content-end mb-0">
    {{-- Previous --}}
    <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
        <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}">
            <i class="bi bi-chevron-left"></i>
        </a>
    </li>

    {{-- Numbered pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
        @else
            @foreach ($element as $page => $url)
                <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
        <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}">
            <i class="bi bi-chevron-right"></i>
        </a>
    </li>
  </ul>
</nav>
@endif
