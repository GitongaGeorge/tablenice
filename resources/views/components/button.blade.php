@props([
    'variant' => 'primary',
    'outlined' => false,
    'size' => 'md',
    'disabled' => false,
    'type' => 'button',
    'class' => '',
    'loadingText' => 'Processing...',
    'icon' => null,
    'theme' => null,
])

@php
    $baseClasses = 'group relative inline-flex justify-center font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl items-center';

    // Base styles for different variants
    $solidVariantClasses = [
        'primary' => 'text-white', // Color is handled by theme or default
        'secondary' => 'text-gray-700 bg-white hover:bg-gray-50 focus:ring-gray-500 border border-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 dark:border-slate-600',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
    ];
    
    // Apply theme to primary variant if it exists
    if ($theme && $variant === 'primary') {
        $solidVariantClasses['primary'] .= ' ' . ($theme['buttonBg'] ?? 'bg-blue-600') . ' ' . ($theme['buttonBgHover'] ?? 'hover:bg-blue-700');
    } else if ($variant === 'primary') {
        $solidVariantClasses['primary'] .= ' bg-blue-600 hover:bg-blue-700';
    }

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm min-h-[32px] gap-1.5',
        'md' => 'px-4 py-2.5 text-sm min-h-[40px] gap-2',
        'lg' => 'px-6 py-3 text-base min-h-[48px] gap-2.5'
    ];

    $variantClasses = $solidVariantClasses[$variant] ?? $solidVariantClasses['primary'];
    $classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses[$size] . ' ' . $class;

    $wireTarget = $attributes->get('wire:target', $attributes->get('wire:click'));
@endphp

<button type="{{ $type }}" class="{{ $classes }}" @if($disabled) disabled @endif {{ $attributes->merge(['wire:loading.attr' => 'disabled']) }}>
    <div class="flex items-center">
        <svg wire:loading @if($wireTarget) wire:target="{{ $wireTarget }}" @endif class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <span wire:loading.remove @if($wireTarget) wire:target="{{ $wireTarget }}" @endif>
            {{ $slot }}
        </span>

        <span wire:loading @if($wireTarget) wire:target="{{ $wireTarget }}" @endif>
            {{ $loadingText }}
        </span>
    </div>
</button>
