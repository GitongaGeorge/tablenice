@props(['field', 'isViewOnly' => false])

<div>
    {{-- Field Label --}}
    <label class="text-sm font-medium text-gray-900 dark:text-slate-200">
        {{ $field->getLabel() }}
    </label>

    <fieldset class="mt-2">
        <legend class="sr-only">{{ $field->getLabel() }}</legend>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($field->getOptions() as $value => $option)
                @php
                    $optionId = $field->getName() . '_' . $value;
                    $color = $option['color'] ?? 'gray';

                    $stylingClasses = [
                        // Base classes for the card
                        'flex items-center justify-center p-4 text-center border-2 rounded-lg transition-all duration-200 shadow-sm transform',
                        $isViewOnly ? 'cursor-not-allowed opacity-60' : 'cursor-pointer group-hover:scale-105',

                        // Unchecked state colors
                        match ($color) {
                            'blue' =>   'border-blue-300 text-blue-700   hover:bg-blue-50   dark:border-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/20',
                            'pink' =>   'border-pink-300 text-pink-700   hover:bg-pink-50   dark:border-pink-700 dark:text-pink-400 dark:hover:bg-pink-900/20',
                            'green' =>  'border-green-400 text-green-700 hover:bg-green-50  dark:border-green-700 dark:text-green-400 dark:hover:bg-green-900/20',
                            'red' =>    'border-red-400 text-red-700     hover:bg-red-50    dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900/20',
                            default =>  'border-gray-300 text-gray-700   hover:bg-gray-50   dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700',
                        },

                        // --- START: MODIFIED CODE ---
                        // Checked state colors, now with hover override to maintain the active background
                        match ($color) {
                            'blue' =>   'peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-600 peer-checked:hover:bg-blue-500',
                            'pink' =>   'peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-600 peer-checked:hover:bg-pink-500',
                            'green' =>  'peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-600 peer-checked:hover:bg-green-500',
                            'red' =>    'peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-600 peer-checked:hover:bg-red-500',
                            default =>  'peer-checked:bg-gray-500 peer-checked:text-white peer-checked:border-gray-600 peer-checked:hover:bg-gray-500',
                        }
                        // --- END: MODIFIED CODE ---
                    ];
                    
                    $badgeClass = match($color) {
                        'blue' => 'bg-blue-600',
                        'pink' => 'bg-pink-600',
                        'green' => 'bg-green-600',
                        'red' => 'bg-red-600',
                        default => 'bg-gray-600',
                    };
                    
                    $isPrefix = $field->getIconPosition()->value === \App\DataTables\Enums\IconPosition::PREFIX->value;
                @endphp
                
                <label for="{{ $optionId }}" class="relative group">
                    <input 
                        type="radio" 
                        wire:model.live="form_data.{{ $field->getName() }}" 
                        id="{{ $optionId }}" 
                        name="{{ $field->getName() }}" 
                        value="{{ $value }}" 
                        class="hidden peer"
                        @if($isViewOnly) disabled @endif
                    >

                    <div @class($stylingClasses)>
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-x-2">
                                @if(isset($option['icon']) && $isPrefix)
                                    <x-icon :name="$option['icon']->value" class="w-5 h-5" />
                                @endif

                                <span class="text-sm font-semibold leading-tight">
                                    {{ $option['label'] }}
                                </span>

                                @if(isset($option['icon']) && !$isPrefix)
                                    <x-icon :name="$option['icon']->value" class="w-5 h-5" />
                                @endif
                            </div>

                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs transition-transform duration-300 scale-0 peer-checked:scale-100 {{ $badgeClass }}">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </fieldset>
</div>