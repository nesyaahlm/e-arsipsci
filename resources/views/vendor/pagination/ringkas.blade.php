@if ($paginator->hasPages())
    @php
        $ukuranBlok = 3;
        $sekarang = $paginator->currentPage();
        $terakhir = $paginator->lastPage();
        $awal = intdiv($sekarang - 1, $ukuranBlok) * $ukuranBlok + 1;
        $akhir = min($awal + $ukuranBlok - 1, $terakhir);
    @endphp

    <nav aria-label="Navigasi halaman">
        <ul class="pagination pagination-sm mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
            @endif

            @for ($i = $awal; $i <= $akhir; $i++)
                @if ($i === $sekarang)
                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endif
            @endfor

            @if ($akhir < $terakhir)
                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($terakhir) }}">{{ $terakhir }}</a></li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif