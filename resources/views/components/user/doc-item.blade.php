@props(['href' => '#', 'title', 'meta' => null, 'badge' => null, 'icon' => 'file'])
<a href="{{ $href }}" class="ud-item">
    <span class="ud-item__icon"><x-user.icon :name="$icon" :size="22" /></span>
    <span class="ud-item__body">
        <span class="ud-item__title">{{ $title }}</span>
        @if ($meta)
            <span class="ud-item__meta">{{ $meta }}</span>
        @endif
    </span>
    @if ($badge)
        <span class="ud-item__badge">{{ $badge }}</span>
    @endif
</a>