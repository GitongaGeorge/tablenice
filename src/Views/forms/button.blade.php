<button
    type="{{ $type }}"
    wire:click="{{ $wireClick }}"
    wire:loading.attr="disabled"
    wire:target="{{ $loadingTarget ?: $wireClick }}"
    @if ($disabled) disabled @endif
    class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium
           @if ($color === 'primary')
               text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500
           @elseif ($color === 'secondary')
               text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-gray-500
           @elseif ($color === 'danger')
               text-white bg-red-600 hover:bg-red-700 focus:ring-red-500
           @elseif ($color === 'success')
               text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
           @elseif ($color === 'warning')
               text-gray-800 bg-yellow-400 hover:bg-yellow-500 focus:ring-yellow-500
           @endif
           focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {{ $attributes->get('class') }}"
>
    @if ($icon)
        <span wire:loading.remove wire:target="{{ $loadingTarget ?: $wireClick }}">
            <x-heroicon-o-{{ $icon }} class="-ml-1 mr-2 h-5 w-5" />
        </span>
    @endif
    <span wire:loading.delay wire:target="{{ $loadingTarget ?: $wireClick }}">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </span>
    <span wire:loading.remove wire:target="{{ $loadingTarget ?: $wireClick }}">{{ $label }}</span>
    <span wire:loading.delay wire:target="{{ $loadingTarget ?: $wireClick }}">Loading...</span>
</button>