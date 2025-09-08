@props(['schema', 'isViewOnly', 'theme'])

@foreach ($schema as $component)
    @if ($component->isVisible())
        @switch(true)
            @case($component instanceof \Mystamyst\TableNice\Forms\Components\Section)
                <div
                    class="p-6 space-y-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-slate-800/50 dark:border-slate-700">
                    <div class="flex items-start space-x-4">
                        @if ($component->getIcon())
                            <div
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-100 rounded-full dark:bg-blue-900/50">
                                <x-tablenice-icon :name="$component->getIcon()->value" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $component->getTitle() }}</h3>
                            @if ($component->getSubtitle())
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">{{ $component->getSubtitle() }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-6">
                        <x-datatable.form-schema :schema="$component->getSchema()" :isViewOnly="$isViewOnly" :theme="$theme" />
                    </div>
                </div>
            @break

            @case($component instanceof \Mystamyst\TableNice\Forms\Components\FieldSet)
                <fieldset class="p-6 border border-gray-200 rounded-lg dark:border-slate-700">
                    @if ($component->getLabel())
                        <legend class="px-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $component->getLabel() }}
                        </legend>
                    @endif
                    <div class="grid grid-cols-12 gap-6 mt-4">
                        <x-datatable.form-schema :schema="$component->getSchema()" :isViewOnly="$isViewOnly" :theme="$theme" />
                    </div>
                </fieldset>
            @break

            @case($component instanceof \Mystamyst\TableNice\Forms\Fields\Field)
                <div @class([
                    'col-span-12' => $component->getColumnSpan() === 12,
                    'col-span-12 sm:col-span-6' => $component->getColumnSpan() === 6,
                    'col-span-12 sm:col-span-4' => $component->getColumnSpan() === 4,
                    'col-span-12 sm:col-span-3' => $component->getColumnSpan() === 3,
                    'col-span-12 sm:col-span-2' => $component->getColumnSpan() === 2,
                    'col-span-12 sm:col-span-1' => $component->getColumnSpan() === 1,
                    'sm:col-start-1' => $component->shouldStartOnNewRow(),
                ])>
                    @include($component->getView(), [
                        'field' => $component,
                        'isViewOnly' => $isViewOnly,
                        'theme' => $theme,
                    ])
                    @error('form_data.' . $component->getName())
                        <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            @break
        @endswitch
    @endif
@endforeach
