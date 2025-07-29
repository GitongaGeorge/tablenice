<div
    @if ($collapsible)
        x-data="{ collapsed: @entangle('collapsed').live }"
        class="bg-white shadow sm:rounded-lg mb-6"
    @else
        class="bg-white shadow sm:rounded-lg mb-6"
    @endif
>
    <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
        <h3 class="text-lg font-medium leading-6 text-gray-900">
            {{ $title }}
        </h3>
        @if ($collapsible)
            <button type="button" @click="collapsed = !collapsed" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">{{ $collapsed ? 'Expand' : 'Collapse' }} section</span>
                <span x-show="!collapsed">
                    <x-heroicon-o-chevron-up class="h-6 w-6" />
                </span>
                <span x-show="collapsed" style="display: none;">
                    <x-heroicon-o-chevron-down class="h-6 w-6" />
                </span>
            </button>
        @endif
    </div>
    @if ($description)
        <div class="px-4 py-2 sm:px-6 text-sm text-gray-500 border-b border-gray-200">
            {{ $description }}
        </div>
    @endif

    <div
        @if ($collapsible)
            x-show="!collapsed"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="px-4 py-5 sm:p-6"
        @else
            class="px-4 py-5 sm:p-6"
        @endif
    >
        {{ $slot }}
    </div>
</div>