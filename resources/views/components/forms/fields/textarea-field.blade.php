{{-- resources/views/components/forms/fields/textarea-field.blade.php --}}
<div>
    <label for="{{ $field->getName() }}" class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ $field->getLabel() }}</label>
    <div class="mt-1">
        <textarea 
            id="{{ $field->getName() }}" 
            rows="4"
            wire:model.live="form_data.{{ $field->getName() }}"
            @if($isViewOnly) disabled @endif
            @class([
                'block w-full sm:text-sm rounded-md shadow-sm bg-white/50 border-0 transition-all duration-200 focus:ring-2 dark:bg-slate-900/50 dark:text-slate-200 dark:placeholder-slate-400',
                'px-4 py-3', // ADDED: Padding
                $theme['ring'] ?? 'focus:ring-indigo-500',
                'ring-1 ring-inset ring-gray-300 dark:ring-slate-700' => !$errors->has('form_data.' . $field->getName()),
                'ring-1 ring-inset ring-red-500' => $errors->has('form_data.' . $field->getName()),
            ])
        ></textarea>
    </div>
</div>
