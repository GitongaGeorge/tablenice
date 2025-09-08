<div>
    @if($this->getForm())
        <form wire:submit.prevent="save">
            <div class="space-y-8">
                {{-- Recursively render the form schema --}}
                <x-datatable.form-schema 
                    :schema="$this->getForm()->getFields()" 
                    :isViewOnly="$isViewOnly" 
                    :theme="$theme" 
                />
            </div>

            <div class="flex justify-end pt-8 mt-8 space-x-3 border-t border-gray-200 dark:border-slate-700">
                @if($isViewOnly)
                    <button 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $theme['ring'] ?? 'focus:ring-indigo-500' }} dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 dark:hover:bg-slate-600"
                        wire:click="$dispatch('closeModal')"
                        type="button"
                    >
                        Close
                    </button>
                @else
                    <button 
                         class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $theme['ring'] ?? 'focus:ring-indigo-500' }} dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 dark:hover:bg-slate-600"
                        wire:click="$dispatch('closeModal')"
                        type="button"
                    >
                        Cancel
                    </button>
                    
                    <button 
                        @class([
                            'inline-flex justify-center px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                            $theme['buttonBg'] ?? 'bg-indigo-600',
                            $theme['buttonBgHover'] ?? 'hover:bg-indigo-700',
                            $theme['ring'] ?? 'focus:ring-indigo-500',
                        ])
                        type="submit" 
                    >
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                @endif
            </div>
        </form>
    @else
        <div class="text-center text-red-500">
            Could not load form.
        </div>
    @endif
</div>

