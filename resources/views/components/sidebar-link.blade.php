@props(['active', 'icon', 'label'])

@php
$classes = ($active ?? false)
    ? 'bg-secondary-container text-on-secondary-container font-semibold'
    : 'text-on-secondary-container/70 hover:bg-surface-container-high';
@endphp

<a {{ $attributes->merge(['class' => "flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 relative {$classes}"]) }}
   :class="sidebarOpen || isMobile ? '' : 'justify-center'">
    <span class="material-symbols-outlined text-2xl flex-shrink-0">{{ $icon }}</span>
    <span x-show="sidebarOpen || isMobile" class="text-sm font-medium truncate flex-1">{{ $label }}</span>
    @isset($badge)
        {{ $badge }}
    @endisset
</a>
