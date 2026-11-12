@props(['paginator'])
@if ($paginator->hasPages())
    <nav class="dk-pager" aria-label="Navigasi halaman">
        <span class="dk-pager__info">
            Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </span>

        <div class="dk-pager__nav">
            @if ($paginator->onFirstPage())
                <span class="dk-pager__btn is-disabled">Sebelumnya</span>
            @else
                <a class="dk-pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
            @endif

            <span class="dk-pager__page">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a class="dk-pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
            @else
                <span class="dk-pager__btn is-disabled">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif