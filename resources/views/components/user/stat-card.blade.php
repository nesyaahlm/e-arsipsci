@props(['label', 'value', 'hint' => null, 'icon' => 'file', 'utama' => false])
<div class="ud-stat {{ $utama ? 'ud-stat--utama' : '' }}">
    <span class="ud-stat__icon"><x-user.icon :name="$icon" :size="22" /></span>
    <div class="ud-stat__body">
        <span class="ud-stat__label">{{ $label }}</span>
        <strong class="ud-stat__value">{{ number_format((int) $value, 0, ',', '.') }}</strong>
        @if ($hint)
            <span class="ud-stat__hint">{{ $hint }}</span>
        @endif
    </div>
</div>