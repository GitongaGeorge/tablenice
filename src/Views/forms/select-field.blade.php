<div class="mb-4">
    <label for="{{ $field->getName() }}" class="block text-sm font-medium text-gray-700">
        {{ $field->getLabel() }}
        @if ($field->isRequired())
            <span class="text-red-500">*</span>
        @endif
    </label>
    <select
        id="{{ $field->getName() }}"
        wire:model.live="formData.{{ $field->getName() }}"
        @if ($field->isMultiple()) multiple @endif
        @if ($field->isDisabled()) disabled @endif
        @if ($field->isReadOnly()) readonly @endif
        @foreach ($field->getExtraAttributes() as $key => $val)
            {{ $key }}="{{ $val }}"
        @endforeach
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
               @error('formData.' . $field->getName()) border-red-500 @enderror"
    >
        @if (!$field->isMultiple())
            <option value="">Select {{ $field->getLabel() }}</option>
        @endif
        @foreach ($field->getOptions() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    @error('formData.' . $field->getName())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>