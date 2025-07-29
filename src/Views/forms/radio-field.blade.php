<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->getLabel() }}
        @if ($field->isRequired())
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="mt-1 space-y-2">
        @foreach ($field->getOptions() as $value => $label)
            <div class="flex items-center">
                <input
                    id="{{ $field->getName() }}_{{ $value }}"
                    name="{{ $field->getName() }}"
                    type="radio"
                    wire:model.live="formData.{{ $field->getName() }}"
                    value="{{ $value }}"
                    @if ($field->isDisabled()) disabled @endif
                    @if ($field->isReadOnly()) readonly @endif
                    @foreach ($field->getExtraAttributes() as $key => $val)
                        {{ $key }}="{{ $val }}"
                    @endforeach
                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500
                           @error('formData.' . $field->getName()) border-red-500 @enderror"
                >
                <label for="{{ $field->getName() }}_{{ $value }}" class="ml-2 block text-sm text-gray-900">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
    @error('formData.' . $field->getName())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>