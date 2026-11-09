@switch(strtolower($nama))
    @case('sertifikasi')
        <svg viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 22l5-3 5 3-1.5-8"/></svg>
        @break
    @case('inspeksi')
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-5-5"/></svg>
        @break
    @case('pengujian')
        <svg viewBox="0 0 24 24"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/></svg>
        @break
    @case('konsultasi')
        <svg viewBox="0 0 24 24"><path d="M4 5h16v11H9l-5 4z"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24"><path d="M3 6h6l2 2h10v11H3z"/></svg>
@endswitch
