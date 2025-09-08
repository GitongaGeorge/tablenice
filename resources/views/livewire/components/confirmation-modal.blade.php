<div x-data="{ show: $wire.entangle('show') }" x-show="show" x-on:keydown.escape.window="$wire.close()"
    style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:px-6">

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm" @click="$wire.close()">
        </div>

        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg p-6 overflow-hidden text-left align-middle transform transition-all bg-white border border-gray-200 shadow-2xl rounded-2xl dark:bg-slate-800 dark:border-slate-700"
            @click.stop>

            <div class="sm:flex sm:items-start">
                <div
                    class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10 dark:bg-red-900/50">
                    <x-tablenice-icon name="heroicon-o-exclamation-triangle"
                        class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                        {{ $title }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $message }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" wire:click="confirm" @class([
                    'inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto',
                    $theme['buttonBg'] ?? '',
                    $theme['buttonBgHover'] ?? ''
                ])>
                    {{ $confirmButtonText }}
                </button>
                <button type="button" wire:click="close"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 dark:hover:bg-slate-600">
                    {{ $cancelButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>