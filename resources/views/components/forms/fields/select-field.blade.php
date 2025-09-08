{{-- resources/views/components/forms/fields/select-field.blade.php --}}
<div>
    <label for="{{ $field->getName() }}" class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ $field->getLabel() }}</label>
    <div class="mt-1 relative">
        <select 
            id="{{ $field->getName() }}" 
            wire:model.live="form_data.{{ $field->getName() }}"
            @if($isViewOnly) disabled @endif
            @class([
                'appearance-none block w-full sm:text-sm rounded-md shadow-sm bg-white/50 border-0 transition-all duration-200 focus:ring-2 dark:bg-slate-900/50 dark:text-slate-200',
                $theme['ring'] ?? 'focus:ring-indigo-500',
                'ring-1 ring-inset ring-gray-300 dark:ring-slate-700' => !$errors->has('form_data.' . $field->getName()),
                'ring-1 ring-inset ring-red-500' => $errors->has('form_data.' . $field->getName()),
            ])
        >
            <option value="">Select an option</option>
            @foreach($field->getOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-slate-400">
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
    </div>
</div>