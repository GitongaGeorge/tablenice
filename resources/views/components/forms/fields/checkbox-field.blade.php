{{-- resources/views/components/forms/fields/checkbox-field.blade.php --}}
<div class="flex items-center">
    <input 
        id="{{ $field->getName() }}" 
        type="checkbox"
        wire:model.live="form_data.{{ $field->getName() }}"
        @if($isViewOnly) disabled @endif
        @class([
            'h-4 w-4 rounded border-gray-300 dark:bg-slate-700 dark:border-slate-600',
            $theme['checkbox'] ?? 'text-indigo-600 focus:ring-indigo-500'
        ])
    >
    <label for="{{ $field->getName() }}" class="ml-2 block text-sm text-gray-900 dark:text-slate-300">{{ $field->getLabel() }}</label>
</div>