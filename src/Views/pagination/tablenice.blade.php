@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
            <div class="flex flex-1 items-center justify-between sm:hidden">
                {{-- Mobile Previous/Next --}}
                <div class="flex items-center space-x-2">
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 cursor-default rounded-full transform transition-transform">
                            <x-dynamic-component :component="'heroicon-s-chevron-left'" class="h-5 w-5" />
                        </span>
                    @else
                        <button wire:click.prevent="previousPage" 
                        class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 rounded-full 
                            shadow-[0_4px_12px_rgba(0,0,0,0.15)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.3)]
                            hover:shadow-[0_6px_16px_rgba(37,99,235,0.25)] dark:hover:shadow-[0_6px_16px_rgba(59,130,246,0.4)]
                            hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300
                            transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1">
                            <x-dynamic-component :component="'heroicon-s-chevron-left'" class="h-5 w-5" />
                        </button>
                    @endif
                
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-full shadow-sm">
                        {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                    </span>
                
                    @if ($paginator->hasMorePages())
                        <button wire:click.prevent="nextPage"
                        class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 rounded-full 
                            shadow-[0_4px_12px_rgba(0,0,0,0.15)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.3)]
                            hover:shadow-[0_6px_16px_rgba(37,99,235,0.25)] dark:hover:shadow-[0_6px_16px_rgba(59,130,246,0.4)]
                            hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300
                            transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1">
                            <x-dynamic-component :component="'heroicon-s-chevron-right'" class="h-5 w-5" />
                        </button>
                    @else
                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 cursor-default rounded-full transform transition-transform">
                            <x-dynamic-component :component="'heroicon-s-chevron-right'" class="h-5 w-5" />
                        </span>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
                <div class="flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 cursor-default rounded-full transform transition-transform">
                            <x-dynamic-component :component="'heroicon-s-chevron-left'" class="h-5 w-5" />
                        </span>
                    @else
                        <button wire:click.prevent="previousPage" rel="prev"
                        class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 rounded-full 
                            shadow-[0_4px_12px_rgba(0,0,0,0.15)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.3)]
                            hover:shadow-[0_6px_16px_rgba(37,99,235,0.25)] dark:hover:shadow-[0_6px_16px_rgba(59,130,246,0.4)]
                            hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300
                            transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1">
                            <x-dynamic-component :component="'heroicon-s-chevron-left'" class="h-5 w-5" />
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="flex items-center space-x-2 px-2">
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true" class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800/80 cursor-default rounded-full">
                                    {{ $element }}
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page" 
                                            class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 cursor-default rounded-full
                                                shadow-[0_4px_12px_rgba(37,99,235,0.35)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.45)]
                                                transform scale-110 -translate-y-1 animate-pulse-subtle">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button wire:click.prevent="gotoPage({{ $page }})"
                                        class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 rounded-full 
                                            shadow-[0_4px_12px_rgba(0,0,0,0.15)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.3)]
                                            hover:shadow-[0_6px_16px_rgba(37,99,235,0.25)] dark:hover:shadow-[0_6px_16px_rgba(59,130,246,0.4)]
                                            hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300
                                            transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click.prevent="nextPage" rel="next"
                        class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 rounded-full 
                            shadow-[0_4px_12px_rgba(0,0,0,0.15)] dark:shadow-[0_4px_12px_rgba(59,130,246,0.3)]
                            hover:shadow-[0_6px_16px_rgba(37,99,235,0.25)] dark:hover:shadow-[0_6px_16px_rgba(59,130,246,0.4)]
                            hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300
                            transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1">
                            <x-dynamic-component :component="'heroicon-s-chevron-right'" class="h-5 w-5" />
                        </button>
                    @else
                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 cursor-default rounded-full transform transition-transform">
                            <x-dynamic-component :component="'heroicon-s-chevron-right'" class="h-5 w-5" />
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>