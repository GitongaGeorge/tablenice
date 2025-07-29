<div class="mb-4">
    <label for="{{ $field->getName() }}" class="block text-sm font-medium text-gray-700">
        {{ $field->getLabel() }}
        @if ($field->isRequired())
            <span class="text-red-500">*</span>
        @endif
    </label>
    <textarea
        id="{{ $field->getName() }}"
        wire:model.live.blur="formData.{{ $field->getName() }}"
        rows="{{ $field->getRows() }}"
        @if ($field->isDisabled()) disabled @endif
        @if ($field->isReadOnly()) readonly @endif
        @foreach ($field->getExtraAttributes() as $key => $val)
            {{ $key }}="{{ $val }}"
        @endforeach
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
               @error('formData.' . $field->getName()) border-red-500 @enderror"
    ></textarea>
    @error('formData.' . $field->getName())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>