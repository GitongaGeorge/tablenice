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
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
                <div class="flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span @class([
                            'relative inline-flex items-center justify-center w-10 h-10 text-sm cursor-default rounded-full',
                            $this->theme['paginationDisabledButtonBg'],
                            $this->theme['paginationDisabledButtonText']
                        ])>
                            <x-icon name="heroicon-s-chevron-left" class="w-5 h-5" />
                        </span>
                    @else
                        <button wire:click.prevent="previousPage" rel="prev"
                        @class([
                            'relative inline-flex items-center justify-center w-10 h-10 text-sm rounded-full shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1',
                            $this->theme['paginationButtonBg'],
                            $this->theme['paginationButtonText']
                        ])>
                            <x-icon name="heroicon-s-chevron-left" class="w-5 h-5" />
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="flex items-center space-x-2 px-2">
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span aria-disabled="true" @class([
                                    'relative inline-flex items-center justify-center w-10 h-10 text-sm cursor-default rounded-full',
                                    $this->theme['paginationButtonBg'],
                                    $this->theme['paginationButtonText']
                                ])>
                                    {{ $element }}
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page" @class([
                                            'relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium cursor-default rounded-full shadow-lg transform scale-110 -translate-y-1',
                                            $this->theme['paginationActiveButtonBg'],
                                            $this->theme['paginationActiveButtonText']
                                        ])>
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button wire:click.prevent="gotoPage({{ $page }})"
                                        @class([
                                            'relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1',
                                            $this->theme['paginationButtonBg'],
                                            $this->theme['paginationButtonText']
                                        ])>
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
                        @class([
                            'relative inline-flex items-center justify-center w-10 h-10 text-sm rounded-full shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-110 hover:-translate-y-1',
                            $this->theme['paginationButtonBg'],
                            $this->theme['paginationButtonText']
                        ])>
                            <x-icon name="heroicon-s-chevron-right" class="w-5 h-5" />
                        </button>
                    @else
                        <span @class([
                            'relative inline-flex items-center justify-center w-10 h-10 text-sm cursor-default rounded-full',
                            $this->theme['paginationDisabledButtonBg'],
                            $this->theme['paginationDisabledButtonText']
                        ])>
                            <x-icon name="heroicon-s-chevron-right" class="w-5 h-5" />
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>

