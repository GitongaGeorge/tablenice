<div x-data="{
    actionsColumn: {
        isCollapsed: window.innerWidth < 768,
        toggle() { this.isCollapsed = !this.isCollapsed; }
    }
}" @class([
    'min-h-screen',
    $this->theme['pageBg'] ?? 'bg-slate-100 dark:bg-slate-900',
])>

    <div class="p-6 mx-auto max-w-7xl lg:p-8">

        {{-- Summary Cards --}}
        @if (!empty($this->summaryCards))
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($this->summaryCards as $card)
                    <div @class([
                        'relative flex flex-col overflow-hidden transition-all duration-300 group rounded-2xl backdrop-blur-sm hover:-translate-y-1',
                        $this->theme['cardBg'],
                        $this->theme['cardBorder'],
                        $this->theme['cardShadow'],
                    ])>
                        {{-- Loading Skeleton --}}
                        <div wire:loading.delay.longer
                            wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage"
                            class="w-full h-full flex flex-col justify-between">
                            <div class="p-6 animate-pulse">
                                <div class="h-4 bg-slate-200 rounded w-3/4 mb-4 dark:bg-slate-700"></div>
                                <div class="h-8 bg-slate-200 rounded w-1/2 mb-2 dark:bg-slate-700"></div>
                                <div class="h-4 bg-slate-200 rounded w-1/3 dark:bg-slate-700"></div>
                            </div>
                            <div class="px-4 pb-4 mt-auto h-40 flex items-center justify-center animate-pulse">
                                <div class="w-full h-full bg-slate-200 rounded-lg dark:bg-slate-700"></div>
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div wire:loading.remove.delay.longer
                            wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage"
                            class="flex flex-col flex-grow h-full">
                            <div class="relative p-6 flex-grow">
                                <dt class="text-sm font-medium {{ $card['titleColor'] }}">
                                    {{ $card['title'] }}
                                </dt>
                                <dd class="text-3xl font-bold {{ $card['valueColor'] }}">
                                    {{ $card['value'] }}
                                </dd>
                                @if ($card['subtitle'])
                                    <p class="text-sm font-medium mt-2 {{ $card['subtitleColor'] }}">
                                        {{ $card['subtitle'] }}
                                    </p>
                                @endif
                            </div>

                            @if (!empty($card['chartConfig']))
                                <div class="relative px-4 pb-4 mt-auto h-40">
                                    <canvas x-data='cardChart(@json($card['chartConfig']))'
                                        wire:key="chart-{{ $card['title'] }}-{{ rand() }}"></canvas>
                                </div>
                            @endif

                            <div @class([
                                'absolute bottom-0 left-0 w-full h-1 transition-transform duration-300 transform scale-x-0 group-hover:scale-x-100',
                                $this->theme['buttonBg'],
                            ])></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Header Controls: Search, Filters, Bulk Actions --}}
        <div class="flex flex-col items-center justify-between gap-6 mb-8 lg:flex-row">

            {{-- Left Side: Search, Filters, Settings --}}
            <div class="flex items-center w-full gap-3 lg:w-auto lg:flex-1">
                {{-- Search Input --}}
                <div class="relative w-full lg:max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none z-10">
                        <svg @class(['w-5 h-5', $this->theme['text']]) xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="{{ config('tablenice.search_placeholder', 'Search Records...') }}" @class([
                            'w-full py-3 pl-12 pr-4 text-sm transition-all duration-200 border-0 shadow-lg rounded-xl focus:ring-2 dark:text-slate-200',
                            $this->theme['inputBg'],
                            $this->theme['ring'],
                            $this->theme['inputPlaceholder'],
                        ])>
                </div>


                {{-- Filters & Settings Buttons --}}
                <div class="flex items-center gap-3">
                    {{-- Filters & Grouping --}}
                    @if (!empty($this->availableGroups) || $this->hasFilterableColumns)
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @class([
                                'transition-all duration-200 border-0 shadow-lg group rounded-xl hover:shadow-xl',
                                'p-3' => !$this->table->showFiltersButtonLabel(),
                                'px-4 py-3 flex items-center gap-2' => $this->table->showFiltersButtonLabel(),
                                $this->theme['controlButtonBg'],
                            ])>
                                <x-tablenice-icon name="heroicon-s-adjustments-horizontal" @class(['w-5 h-5 transition-colors', $this->theme['text']]) />
                                @if ($this->table->showFiltersButtonLabel())
                                    <span @class(['text-sm font-semibold', $this->theme['text']])>Filters</span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition @class([
                                'absolute left-0 z-50 mt-2 origin-top-left shadow-2xl w-80 rounded-2xl backdrop-blur-xl ring-1 lg:origin-top-right lg:left-auto lg:right-0',
                                $this->theme['tbody'],
                                $this->theme['pagination'],
                            ])
                                style="display: none;">
                                <div class="p-6 space-y-6">
                                    {{-- Clear Filters Button --}}
                                    @if ($this->hasActiveFilters)
                                        <button wire:click.prevent="clearFilters" @class([
                                            'text-sm font-medium hover:underline w-full text-left',
                                            $this->theme['text'],
                                        ])>
                                            Clear All Filters
                                        </button>
                                        <hr class="border-gray-200 dark:border-gray-700">
                                    @endif
                                    @if (!empty($this->availableGroups()))
                                        <div>
                                            <label
                                                class="block mb-3 text-sm font-semibold text-blue-900 dark:text-blue-200">Group
                                                By</label>
                                            <div class="relative">
                                                <select wire:model.live="activeGroup" @class([
                                                    'w-full pl-4 pr-10 py-3 text-sm text-slate-800 border-0 rounded-xl transition-all duration-200 focus:ring-2 dark:text-slate-200 appearance-none',
                                                    $this->theme['selectBg'],
                                                    $this->theme['ring'],
                                                ])>
                                                    <option value="">-- No Grouping --</option>
                                                    @foreach ($this->availableGroups() as $group)
                                                        <option value="{{ $group->name }}">{{ $group->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                                    <x-tablenice-icon name="heroicon-s-chevron-up-down" class="w-4 h-4" />
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @foreach ($this->columnsForView as $column)
                                        @if ($column->isFilterable())
                                            <div>
                                                <label
                                                    class="block mb-3 text-sm font-semibold text-blue-900 dark:text-blue-200">{{ $column->getLabel() }}</label>
                                                <div class="relative">
                                                    <select wire:model.live="filters.{{ $column->getName() }}" @class([
                                                        'w-full pl-4 pr-10 py-3 text-sm text-slate-800 border-0 rounded-xl transition-all duration-200 focus:ring-2 dark:text-slate-200 appearance-none',
                                                        $this->theme['selectBg'],
                                                        $this->theme['ring'],
                                                    ])>
                                                        <option value="">All</option>
                                                        @foreach ($column->getFilterOptions() as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                                        <x-tablenice-icon name="heroicon-s-chevron-up-down" class="w-4 h-4" />
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- Settings --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @class([
                            'transition-all duration-200 border-0 shadow-lg group rounded-xl hover:shadow-xl',
                            'p-3' => !$this->table->showSettingsButtonLabel(),
                            'px-4 py-3 flex items-center gap-2' => $this->table->showSettingsButtonLabel(),
                            $this->theme['controlButtonBg'],
                        ])>
                            <x-tablenice-icon name="heroicon-s-cog-6-tooth" @class(['w-5 h-5 transition-colors', $this->theme['text']]) />
                            @if ($this->table->showSettingsButtonLabel())
                                <span @class(['text-sm font-semibold', $this->theme['text']])>Settings</span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition @class([
                            'absolute right-0 z-50 w-64 mt-2 origin-top-right shadow-2xl rounded-2xl backdrop-blur-xl ring-1',
                            $this->theme['tbody'],
                            $this->theme['pagination'],
                        ]) style="display: none;">
                            <div class="p-6 space-y-6">
                                <div>
                                    <label
                                        class="block mb-3 text-sm font-semibold text-blue-900 dark:text-blue-200">Items
                                        per Page</label>
                                    <div class="relative">
                                        <select wire:model.live="perPage" @class([
                                            'w-full pl-4 pr-10 py-3 text-sm text-slate-800 border-0 rounded-xl transition-all duration-200 focus:ring-2 dark:text-slate-200 appearance-none',
                                            $this->theme['selectBg'],
                                            $this->theme['ring'],
                                        ])>
                                            @foreach(config('tablenice.pagination.per_page_options', [10, 25, 50, 100]) as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                            <x-tablenice-icon name="heroicon-s-chevron-up-down" class="w-4 h-4" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-blue-900 dark:text-blue-200">Sticky
                                            Header</span>
                                        <input type="checkbox" wire:model.live="stickyHeader" @class(['w-4 h-4 border-gray-300 rounded', $this->theme['checkbox']])>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Page Actions and Bulk Actions --}}
            <div class="flex items-center flex-shrink-0 gap-3">
                {{-- Bulk Actions --}}
                <div x-show="$wire.selectedRows.length > 0" style="display: none;" class="flex flex-col items-start">
                    <div x-data="{ open: false }" class="relative inline-block">
                        <button @click="open = !open" @class([
                            'inline-flex items-center gap-2 px-3 py-3 text-sm font-semibold text-white transition-all duration-200 rounded-xl shadow-lg hover:shadow-xl',
                            $this->theme['buttonBg'],
                            $this->theme['buttonBgHover'],
                        ])>
                            <x-tablenice-icon name="heroicon-s-bars-3" class="w-5 h-5" />
                            <span>Bulk Actions ({{ count($selectedRows) }})</span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition @class([
                            'absolute z-50 mt-2 origin-top-left shadow-2xl w-60 rounded-2xl backdrop-blur-xl ring-1 lg:origin-top-right lg:left-auto lg:right-0',
                            $this->theme['tbody'],
                            $this->theme['pagination'],
                        ])
                            style="display: none;">
                            <div class="p-2 space-y-1">
                                @foreach ($this->table->bulkActions() as $action)
                                    <button
                                        wire:click="$set('activeBulkAction', '{{ $action->getName() }}'); $nextTick(() => $wire.handleBulkAction())"
                                        @class([
                                            'flex items-center w-full text-left transition-colors duration-200 focus:outline-none rounded-lg',
                                            'p-2' => $action->isIconOnly(),
                                            'gap-3 px-4 py-2 text-sm font-semibold' => !$action->isIconOnly(),
                                            $action->getButtonClasses('button'),
                                            'text-white' => !$action->getColor(),
                                            $this->theme['buttonBg'] => !$action->getColor(),
                                            $this->theme['buttonBgHover'] => !$action->getColor(),
                                        ])>
                                        @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::PREFIX)
                                            {!! $action->getIconHtml() !!}
                                        @endif
                                        @if (!$action->isIconOnly())
                                            <span>{{ $action->getLabel() }}</span>
                                        @endif
                                        @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::SUFFIX)
                                            {!! $action->getIconHtml() !!}
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button wire:click.prevent="clearSelectedRows"
                        class="mt-1 text-xs font-medium {{ $this->theme['text'] }} hover:underline">
                        Deselect All
                    </button>
                </div>

                @foreach ($this->table->pageActions() as $action)
                    <button wire:click="handleAction('{{ $action->getName() }}')" type="button" @class([
                        'inline-flex items-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg shadow-lg hover:shadow-xl',
                        'p-3' => $action->isIconOnly(),
                        'gap-2 px-3 py-2.5 text-sm font-semibold' => !$action->isIconOnly(),
                        $action->getButtonClasses('button'),
                        'text-white' => !$action->getColor(),
                        $this->theme['buttonBg'] => !$action->getColor(),
                        $this->theme['buttonBgHover'] => !$action->getColor(),
                        $this->theme['ring'],
                    ])>
                        @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::PREFIX)
                            {!! $action->getIconHtml() !!}
                        @endif
                        @if (!$action->isIconOnly())
                            <span>{{ $action->getLabel() }}</span>
                        @endif
                        @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::SUFFIX)
                            {!! $action->getIconHtml() !!}
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Table Container --}}
        <div @class([
            'overflow-hidden shadow-2xl rounded-2xl backdrop-blur-sm ring-1',
            $this->theme['tableContainer'],
        ])>
            <div class="overflow-x-auto">
                <table class="min-w-full border-spacing-2">
                    <thead @class([
                        $this->theme['headerBg'],
                        'sticky top-0 z-40' => $this->stickyHeader,
                    ])>
                        <tr>
                            <th scope="col" @class([
                                'sticky left-0 z-30 px-6 py-4',
                                $this->theme['headerBgSolid'],
                            ])
                                style="width: 60px;">
                                <input type="checkbox" wire:model.live="selectAll" @class([
                                    'w-5 h-5 border-gray-300 rounded dark:bg-slate-700 dark:border-slate-600',
                                    $this->theme['checkbox'],
                                ])>
                            </th>
                            @foreach ($this->columnsForView as $column)
                                                        <th scope="col" @class([
                                                            'px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase',
                                                            'sticky z-20' => $column->isSticky(),
                                                            $this->theme['headerBgSolid'] => $column->isSticky(),
                                                        ]) @php
                                    $styles = [];
                                    if ($column->isSticky())
                                        $styles[] = "left: {$column->stickyOffset}px";
                                    if ($column->getWidth())
                                        $styles[] = "min-width: {$column->getWidth()}";
                                @endphp @if (!empty($styles)) style="{{ implode('; ', $styles) }}" @endif>
                                                            @if ($column->isSortable())
                                                                <button wire:click="sortBy('{{ $column->getName() }}')"
                                                                    class="flex items-center gap-2 transition-colors duration-200 group hover:text-blue-100">
                                                                    <span>{{ $column->getLabel() }}</span>
                                                                    <span class="flex-none w-4 h-4">
                                                                        @if ($sortField === $column->getName())
                                                                            @if ($sortDirection === 'asc')
                                                                                <x-tablenice-icon name="heroicon-s-chevron-up" class="w-4 h-4" />
                                                                            @else
                                                                                <x-tablenice-icon name="heroicon-s-chevron-down" class="w-4 h-4" />
                                                                            @endif
                                                                        @else
                                                                            <x-tablenice-icon name="heroicon-s-chevron-up-down"
                                                                                class="w-4 h-4 opacity-50" />
                                                                        @endif
                                                                    </span>
                                                                </button>
                                                            @else
                                                                <span>{{ $column->getLabel() }}</span>
                                                            @endif
                                                        </th>
                            @endforeach
                            <th scope="col" @class([
                                'sticky right-0 z-30 px-2 py-3',
                                $this->theme['headerBgSolid'],
                            ])>
                                <div class="flex items-center justify-center">
                                    <button @click="actionsColumn.toggle()"
                                        class="p-2 hidden md:flex text-white rounded-full hover:bg-white/10">
                                        <span x-show="actionsColumn.isCollapsed"><x-tablenice-icon
                                                name="heroicon-o-arrows-pointing-out" class="w-5 h-5" /></span>
                                        <span x-show="!actionsColumn.isCollapsed"
                                            style="display: none;"><x-tablenice-icon
                                                name="heroicon-o-arrows-pointing-in" class="w-5 h-5" /></span>
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:key="datatable-body" wire:loading.class.delay="opacity-50"
                        wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage"
                        @class([$this->theme['tbody']])>
                        {{-- Loading Skeleton for Table Body --}}
                        <tr wire:loading.delay.longer
                            wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage">
                            <td colspan="{{ count($this->columnsForView) + 2 }}" class="p-0">
                                @foreach (range(1, $perPage) as $i)
                                    <div class="flex items-center justify-between p-6 space-x-6 animate-pulse">
                                        <div class="flex items-center space-x-6">
                                            <div class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700"></div>
                                            <div class="w-24 h-4 rounded bg-slate-200 dark:bg-slate-700"></div>
                                        </div>
                                        <div class="h-4 rounded w-36 bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="h-4 rounded w-24 bg-slate-200 dark:bg-slate-700 hidden md:block">
                                        </div>
                                        <div class="h-4 rounded w-24 bg-slate-200 dark:bg-slate-700 hidden lg:block">
                                        </div>
                                        <div class="h-4 rounded w-24 bg-slate-200 dark:bg-slate-700 hidden xl:block">
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        </tr>

                        {{-- Table Body Content --}}
                        <g wire:loading.remove.delay.longer
                            wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage">
                            @php
                                $activeGroupData = $this->activeGroupData();
                                $hasSummaryAtTop = collect($this->columnsForView)->some(
                                    fn($c) => $c->hasSummary() &&
                                    in_array($c->getSummary()['location'], [
                                        \Mystamyst\TableNice\Enums\SummaryLocation::TOP,
                                        \Mystamyst\TableNice\Enums\SummaryLocation::BOTH,
                                    ]),
                                );
                            @endphp

                            @if ($activeGroupData && $groupedItems)
                                @php $itemIndex = 0; @endphp
                                @forelse($groupedItems as $groupName => $groupItems)
                                    <tr @class([
                                        $this->theme['groupHeaderBg'],
                                        'sticky top-[57px] z-30' => $stickyHeader,
                                    ])>
                                        <th colspan="{{ count($this->columnsForView) + 2 }}"
                                            class="sticky left-0 px-6 py-4 text-sm font-bold text-left">
                                            <div @class(['flex items-center gap-2', $this->theme['groupHeaderText']])>
                                                <div @class(['w-1 h-6 rounded-full', $this->theme['buttonBg']])></div>
                                                {{ $activeGroupData->label }}: {{ $groupName ?: 'N/A' }}
                                            </div>
                                        </th>
                                    </tr>
                                    @php
                                        $isExpanded = in_array($groupName, $this->expandedGroups);
                                        $itemsToShow = $isExpanded ? $groupItems : $groupItems->take($perGroup);
                                    @endphp
                                    @foreach ($itemsToShow as $item)
                                        <tr @class([
                                            'group transition-colors duration-200',
                                            $this->theme['rowHover'],
                                        ])>
                                            <td @class([
                                                'sticky left-0 z-20 px-6 py-4',
                                                $this->theme['stickyCellBg'],
                                                $this->theme['stickyCellHoverBg'],
                                            ])>
                                                <input type="checkbox" wire:model.live="selectedRows"
                                                    value="{{ data_get($item, $this->getKeyName()) }}" @class(['w-5 h-5 border-gray-300 rounded', $this->theme['checkbox']])>
                                            </td>
                                            @foreach ($this->columnsForView as $column)
                                                @if ($column instanceof \Mystamyst\TableNice\Columns\IndexColumn)
                                                    {!! $column->toHtml($item, $itemIndex++) !!}
                                                @else
                                                    {!! $column->toHtml($item) !!}
                                                @endif
                                            @endforeach
                                            <td @class([
                                                'sticky right-0 z-20 text-sm font-medium text-right whitespace-nowrap',
                                                $this->theme['stickyCellBg'],
                                                $this->theme['stickyCellHoverBg'],
                                            ])>
                                                {{-- Responsive Actions --}}
                                                <div class="items-center justify-end hidden gap-1 px-2 md:flex">
                                                    <div x-show="!actionsColumn.isCollapsed" x-transition
                                                        class="flex items-center gap-1">
                                                        @foreach ($this->table->actions() as $action)
                                                            <button
                                                                wire:click="handleAction('{{ $action->getName() }}', '{{ data_get($item, $this->getKeyName()) }}')"
                                                                type="button" @class([
                                                                    'transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 flex items-center',
                                                                    'rounded-lg' => $action->getDisplayType(),
                                                                    'p-2' => $action->isIconOnly() && $action->getDisplayType(),
                                                                    'gap-1.5 px-3 py-1.5 text-xs font-semibold' => !$action->isIconOnly() && $action->getDisplayType(),
                                                                    'p-2 text-gray-400 rounded-full' => !$action->getDisplayType(),
                                                                    $action->getButtonClasses(),
                                                                    $this->theme['rowHover'] => !$action->getDisplayType(),
                                                                    $this->theme['ring'],
                                                                ])>
                                                                @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::PREFIX)
                                                                    {!! $action->getIconHtml() !!}
                                                                @endif
                                                                @if (!$action->isIconOnly())
                                                                    <span>{{ $action->getLabel() }}</span>
                                                                @endif
                                                                @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::SUFFIX)
                                                                    {!! $action->getIconHtml() !!}
                                                                @endif
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div x-data="{ open: false }" class="relative md:hidden">
                                                    <button @click="open = !open"
                                                        class="p-2 -m-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700">
                                                        <x-tablenice-icon name="heroicon-s-ellipsis-vertical" class="w-5 h-5" />
                                                    </button>
                                                    <div x-show="open" @click.away="open = false" x-transition
                                                        class="absolute right-0 z-30 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-slate-800 dark:ring-slate-700"
                                                        style="display: none;">
                                                        <div class="py-1">
                                                            @foreach ($this->table->actions() as $action)
                                                                <a href="#"
                                                                    wire:click.prevent="handleAction('{{ $action->getName() }}', '{{ data_get($item, $this->getKeyName()) }}'); open = false;"
                                                                    class="flex items-center w-full gap-3 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700">
                                                                    @if ($action->getIcon())
                                                                        {!! $action->getIconHtml() !!}
                                                                    @endif
                                                                    <span>{{ $action->getLabel() }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($groupItems->count() > $perGroup)
                                        <tr>
                                            <td colspan="{{ count($this->columnsForView) + 2 }}" class="px-6 py-4 text-center">
                                                <button wire:click="toggleGroup('{{ $groupName }}')" @class([
                                                    'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-all duration-200 rounded-lg shadow-lg hover:shadow-xl',
                                                    $this->theme['buttonBg'],
                                                    $this->theme['buttonBgHover'],
                                                ])>
                                                    {{ $isExpanded ? 'Show Less' : 'Show ' . ($groupItems->count() - $perGroup) . ' More' }}
                                                    <x-tablenice-icon name="heroicon-s-chevron-down" @class([
                                                        'w-4 h-4 transition-transform duration-200',
                                                        'rotate-180' => $isExpanded,
                                                    ]) />
                                                </button>
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($hasSummaryAtTop)
                                        <tr @class(['font-semibold', $this->theme['summaryRowBg']])>
                                            <td @class(['sticky left-0 z-10', $this->theme['stickyCellBg']])
                                                style="min-width: 60px;"></td>
                                            @foreach ($this->columnsForView as $column)
                                                                <td @class([
                                                                    'px-6 py-3 text-sm',
                                                                    $this->theme['summaryRowText'],
                                                                    $column->getAlignmentClass(),
                                                                    'sticky z-10' => $column->isSticky(),
                                                                    $this->theme['stickyCellBg'] => $column->isSticky(),
                                                                ]) @php
                                                    $styles = [];
                                                    if ($column->isSticky())
                                                        $styles[] = "left: {$column->stickyOffset}px";
                                                    if ($column->getWidth())
                                                        $styles[] = "min-width: {$column->getWidth()}";
                                                @endphp @if (!empty($styles)) style="{{ implode('; ', $styles) }}" @endif>
                                                                    @if (
                                                                            $column->hasSummary() &&
                                                                            in_array($column->getSummary()['location'], [
                                                                                \Mystamyst\TableNice\Enums\SummaryLocation::TOP,
                                                                                \Mystamyst\TableNice\Enums\SummaryLocation::BOTH,
                                                                            ])
                                                                        )
                                                                        {{ $this->summaries['groups'][$groupName][$column->getName()] ?? '' }}
                                                                    @endif
                                                                </td>
                                            @endforeach
                                            <td @class(['sticky right-0 z-10', $this->theme['stickyCellBg']])></td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="{{ count($this->columnsForView) + 2 }}" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <x-tablenice-icon name="heroicon-o-circle-stack" @class(['w-12 h-12 mb-4', $this->theme['text']]) />
                                                <p @class(['text-lg font-medium', $this->theme['text']])>No items found in this
                                                    group</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400">Try adjusting
                                                    your search or filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                @if ($hasSummaryAtTop && optional($items)->isNotEmpty())
                                    <tr @class(['font-bold', $this->theme['summaryRowBg']])>
                                        <td @class(['sticky left-0 z-10', $this->theme['stickyCellBg']])
                                            style="min-width: 60px;"></td>
                                        @foreach ($this->columnsForView as $column)
                                                                <td @class([
                                                                    'px-6 py-3 text-sm',
                                                                    $this->theme['summaryRowText'],
                                                                    $column->getAlignmentClass(),
                                                                    'sticky z-10' => $column->isSticky(),
                                                                    $this->theme['stickyCellBg'] => $column->isSticky(),
                                                                ]) @php
                                                $styles = [];
                                                if ($column->isSticky())
                                                    $styles[] = "left: {$column->stickyOffset}px";
                                                if ($column->getWidth())
                                                    $styles[] = "min-width: {$column->getWidth()}";
                                            @endphp @if (!empty($styles)) style="{{ implode('; ', $styles) }}" @endif>
                                                                    @if (
                                                                            $column->hasSummary() &&
                                                                            in_array($column->getSummary()['location'], [
                                                                                \Mystamyst\TableNice\Enums\SummaryLocation::TOP,
                                                                                \Mystamyst\TableNice\Enums\SummaryLocation::BOTH,
                                                                            ])
                                                                        )
                                                                        {{ $this->summaries['total'][$column->getName()] ?? '' }}
                                                                    @endif
                                                                </td>
                                        @endforeach
                                        <td @class(['sticky right-0 z-10', $this->theme['stickyCellBg']])></td>
                                    </tr>
                                @endif
                                @forelse ($items ?? [] as $item)
                                    <tr @class([
                                        'group transition-colors duration-200',
                                        $this->theme['rowHover'],
                                    ])>
                                        <td @class([
                                            'sticky left-0 z-20 px-6 py-4',
                                            $this->theme['stickyCellBg'],
                                            $this->theme['stickyCellHoverBg'],
                                        ])>
                                            <input type="checkbox" wire:model.live="selectedRows"
                                                value="{{ data_get($item, $this->getKeyName()) }}" @class(['w-5 h-5 border-gray-300 rounded', $this->theme['checkbox']])>
                                        </td>
                                        @foreach ($this->columnsForView as $column)
                                            @if ($column instanceof \Mystamyst\TableNice\Columns\IndexColumn)
                                                {!! $column->toHtml($item, $loop->index) !!}
                                            @else
                                                {!! $column->toHtml($item) !!}
                                            @endif
                                        @endforeach
                                        <td @class([
                                            'sticky right-0 z-20 text-sm font-medium text-right whitespace-nowrap',
                                            $this->theme['stickyCellBg'],
                                            $this->theme['stickyCellHoverBg'],
                                        ])>
                                            {{-- Responsive Actions --}}
                                            <div class="items-center justify-end hidden gap-1 px-2 md:flex">
                                                <div x-show="!actionsColumn.isCollapsed" x-transition
                                                    class="flex items-center gap-1">
                                                    @foreach ($this->table->actions() as $action)
                                                        <button
                                                            wire:click="handleAction('{{ $action->getName() }}', '{{ data_get($item, $this->getKeyName()) }}')"
                                                            type="button" @class([
                                                                'transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 flex items-center',
                                                                'rounded-lg' => $action->getDisplayType(),
                                                                'p-2' => $action->isIconOnly() && $action->getDisplayType(),
                                                                'gap-1.5 px-3 py-1.5 text-xs font-semibold' => !$action->isIconOnly() && $action->getDisplayType(),
                                                                'p-2 text-gray-400 rounded-full' => !$action->getDisplayType(),
                                                                $action->getButtonClasses(),
                                                                $this->theme['rowHover'] => !$action->getDisplayType(),
                                                                $this->theme['ring'],
                                                            ])>
                                                            @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::PREFIX)
                                                                {!! $action->getIconHtml() !!}
                                                            @endif
                                                            @if (!$action->isIconOnly())
                                                                <span>{{ $action->getLabel() }}</span>
                                                            @endif
                                                            @if ($action->getIcon() && $action->getIconPosition() === \Mystamyst\TableNice\Enums\Icons\IconPosition::SUFFIX)
                                                                {!! $action->getIconHtml() !!}
                                                            @endif
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div x-data="{ open: false }" class="relative md:hidden">
                                                <button @click="open = !open"
                                                    class="p-2 -m-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700">
                                                    <x-tablenice-icon name="heroicon-s-ellipsis-vertical" class="w-5 h-5" />
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-transition
                                                    class="absolute right-0 z-30 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-slate-800 dark:ring-slate-700"
                                                    style="display: none;">
                                                    <div class="py-1">
                                                        @foreach ($this->table->actions() as $action)
                                                            <a href="#"
                                                                wire:click.prevent="handleAction('{{ $action->getName() }}', '{{ data_get($item, $this->getKeyName()) }}'); open = false;"
                                                                class="flex items-center w-full gap-3 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700">
                                                                @if ($action->getIcon())
                                                                    {!! $action->getIconHtml() !!}
                                                                @endif
                                                                <span>{{ $action->getLabel() }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($this->columnsForView) + 2 }}" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <x-tablenice-icon name="heroicon-o-circle-stack" @class(['w-12 h-12 mb-4', $this->theme['text']]) />
                                                <p @class(['text-lg font-medium', $this->theme['text']])>No items found</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400">Try adjusting
                                                    your search or filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </g>
                    </tbody>
                    @php
                        $hasSummaryAtBottom = collect($this->columnsForView)->some(
                            fn($c) => $c->hasSummary() &&
                            in_array($c->getSummary()['location'], [
                                \Mystamyst\TableNice\Enums\SummaryLocation::BOTTOM,
                                \Mystamyst\TableNice\Enums\SummaryLocation::BOTH,
                            ]),
                        );
                    @endphp
                    @if ($hasSummaryAtBottom && !empty($this->summaries['total']))
                        <tfoot @class([$this->theme['headerBg']])>
                            <tr class="font-bold">
                                <td @class(['sticky left-0 z-20', $this->theme['headerBgSolid']]) style="min-width: 60px;">
                                </td>
                                @foreach ($this->columnsForView as $column)
                                                        <td @class([
                                                            'px-6 py-4 text-sm text-white',
                                                            $column->getAlignmentClass(),
                                                            'sticky z-10' => $column->isSticky(),
                                                            $this->theme['headerBgSolid'] => $column->isSticky(),
                                                        ]) @php
                                        $styles = [];
                                        if ($column->isSticky())
                                            $styles[] = "left: {$column->stickyOffset}px";
                                        if ($column->getWidth())
                                            $styles[] = "min-width: {$column->getWidth()}";
                                    @endphp @if (!empty($styles)) style="{{ implode('; ', $styles) }}" @endif>
                                                            @if (
                                                                    $column->hasSummary() &&
                                                                    in_array($column->getSummary()['location'], [
                                                                        \Mystamyst\TableNice\Enums\SummaryLocation::BOTTOM,
                                                                        \Mystamyst\TableNice\Enums\SummaryLocation::BOTH,
                                                                    ])
                                                                )
                                                                {{ $this->summaries['total'][$column->getName()] ?? '' }}
                                                            @endif
                                                        </td>
                                @endforeach
                                <td @class(['sticky right-0 z-20', $this->theme['headerBgSolid']])></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if (($items && !$groupedItems) || ($groupedItems && $this->paginatedItems()))
            <div class="flex justify-center mt-8" wire:key="pagination-links">
                <div wire:loading.delay.longer
                    wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage"
                    class="flex items-center justify-center p-4">
                    <svg @class(['w-8 h-8 animate-spin', $this->theme['text']]) xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <div wire:loading.remove.delay.longer
                    wire:target="search, filters, activeGroup, perPage, perGroup, sortField, sortDirection, gotoPage, nextPage, previousPage"
                    @class([
                        'p-4 shadow-xl backdrop-blur-sm rounded-2xl',
                        $this->theme['tableContainer'],
                    ])>
                    @if ($items && !$groupedItems)
                        {{ $items->links(config('tablenice.pagination.view', 'tablenice::livewire.pagination.alpha')) }}
                    @elseif($groupedItems)
                        {{ $this->paginatedItems()->links(config('tablenice.pagination.view', 'tablenice::livewire.pagination.tablenice')) }}
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Global Tooltip --}}
    <div id="global-tooltip" x-data x-show="$store.tooltip.visible" x-cloak x-transition role="tooltip" @class([
        'absolute z-50 px-3 py-2 text-sm font-semibold text-white rounded-lg shadow-lg',
        $this->theme['headerBgSolid'] ?? 'bg-gray-900 dark:bg-slate-900',
    ])>
        <div x-html="$store.tooltip.content"></div>
        <div id="arrow" data-popper-arrow></div>
    </div>
</div>