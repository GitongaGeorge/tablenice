<div>
    <label for="{{ $field->getName() }}" class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ $field->getLabel() }}</label>
    <div class="mt-1">
        <x-forms.rich-editor 
            :field="$field" 
            :isViewOnly="$isViewOnly"
            wire:model="form_data.{{ $field->getName() }}"
        />
    </div>
</div>
