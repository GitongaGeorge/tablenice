@php
    $color = $field->getColor()?->name ?? 'GRAY';
    
    $colorClasses = match (strtoupper($color)) {
        'RED' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'YELLOW' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400 text-black',
        'GREEN' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'BLUE' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'INDIGO' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
        'PURPLE' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
        'PINK' => 'bg-pink-600 hover:bg-pink-700 focus:ring-pink-500',
        default => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
    };
@endphp

<div class="flex items-end">
    <button
        type="{{ $field->getType() }}"
        @if($action = $field->getAction())
            wire:click="{{ $action }}"
        @endif
        @if($field->isDisabled()) disabled @endif
        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-colors duration-200 border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-800 {{ $colorClasses }} @if($field->isDisabled()) opacity-50 cursor-not-allowed @endif"
    >
        @if($icon = $field->getIcon())
            {!! $icon->toHtml(['class' => 'w-5 h-5']) !!}
        @endif
        <span>{{ $field->getLabel() }}</span>
    </button>
</div>