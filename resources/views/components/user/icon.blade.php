@props(['name', 'size' => 20, 'stroke' => 2])
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" {{ $attributes }}>
    @switch($name)
        @case('dashboard')
            <rect x="3" y="3" width="8" height="10" rx="1.5"/>
            <rect x="13" y="3" width="8" height="6" rx="1.5"/>
            <rect x="13" y="11" width="8" height="10" rx="1.5"/>
            <rect x="3" y="15" width="8" height="6" rx="1.5"/>
            @break
        @case('search')
            <rect x="3" y="3" width="18" height="18" rx="4"/>
            <circle cx="11" cy="11" r="4"/>
            <path d="m14 14 3 3"/>
            @break
        @case('magnify')
            <circle cx="11" cy="11" r="7"/>
            <path d="m21 21-4.3-4.3"/>
            @break
        @case('upload')
            <path d="M12 16V4"/>
            <path d="m7 9 5-5 5 5"/>
            <path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/>
            @break
        @case('file')
            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 13h6"/>
            <path d="M9 17h6"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21a8 8 0 0 1 16 0"/>
            @break
        @case('avatar')
            <circle cx="12" cy="12" r="10"/>
            <circle cx="12" cy="10" r="3.5"/>
            <path d="M5.5 19a7.5 7.5 0 0 1 13 0"/>
            @break
        @case('menu')
            <path d="M4 6h16"/>
            <path d="M4 12h16"/>
            <path d="M4 18h16"/>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break
        @case('logout')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <path d="m16 17 5-5-5-5"/>
            <path d="M21 12H9"/>
            @break
        @case('star')
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
            @break
        @case('folder')
            <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            @break
    @endswitch
</svg>