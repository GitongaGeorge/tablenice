<div class="mb-4 flex items-center">
    <input
        type="checkbox"
        id="{{ $field->getName() }}"
        wire:model.live="formData.{{ $field->getName() }}"
        @if ($field->isDisabled()) disabled @endif
        @if ($field->isReadOnly()) readonly @endif
        @foreach ($field->getExtraAttributes() as $key => $val)
            {{ $key }}="{{ $val }}"
        @endforeach
        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500
               @error('formData.' . $field->getName()) border-red-500 @enderror"
    >
    <label for="{{ $field->getName() }}" class="ml-2 block text-sm font-medium text-gray-700">
        {{ $field->getLabel() }}
        @if ($field->isRequired())
            <span class="text-red-500">*</span>
        @endif
    </label>
    @error('formData.' . $field->getName())
        <p class="ml-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>