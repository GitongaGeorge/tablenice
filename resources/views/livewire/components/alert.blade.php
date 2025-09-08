{{-- resources/views/livewire/components/alert.blade.php --}}
<div x-data="{ show: $wire.entangle('show'), timeout: null }"
     x-init="
        $watch('show', value => {
            if (value) {
                clearTimeout(timeout);
                timeout = setTimeout(() => { show = false }, 5000);
            }
        })
     "
     x-show="show"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
     class="fixed top-5 right-5 z-[100] w-full max-w-sm">
    
    <div @class([
        'overflow-hidden rounded-xl pointer-events-auto border-t border-white/20',
        'shadow-2xl shadow-slate-900/20 dark:shadow-black/40', // Bulging look
        $this->styles()['container'],
    ])>
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @switch($type)
                        @case('success')
                            <x-icon name="heroicon-o-check-circle" @class(['w-6 h-6', $this->styles()['icon']]) />
                            @break
                        @case('error')
                            <x-icon name="heroicon-o-x-circle" @class(['w-6 h-6', $this->styles()['icon']]) />
                            @break
                        @case('warning')
                            <x-icon name="heroicon-o-exclamation-triangle" @class(['w-6 h-6', $this->styles()['icon']]) />
                            @break
                        @default
                            <x-icon name="heroicon-o-information-circle" @class(['w-6 h-6', $this->styles()['icon']]) />
                    @endswitch
                </div>
                <div class="flex-1 w-0 pt-0.5 ml-3">
                    <p class="text-sm font-semibold">
                        {{ $message }}
                    </p>
                </div>
                <div class="flex flex-shrink-0 ml-4">
                    <button @click="show = false" type="button" class="inline-flex rounded-md text-white/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-gray-800">
                        <span class="sr-only">Close</span>
                        <x-icon name="heroicon-s-x-mark" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
