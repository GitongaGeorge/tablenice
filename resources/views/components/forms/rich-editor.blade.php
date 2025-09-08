{{-- resources/views/components/forms/rich-editor.blade.php --}}
@props(['field', 'isViewOnly' => false])

<div
    x-data="trixEditor({
        content: @entangle($attributes->wire('model')),
        isViewOnly: {{ $isViewOnly ? 'true' : 'false' }},
        placeholder: 'Write something amazing...'
    })"
    x-cloak
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    class="trix-container rounded-lg shadow-sm transition-all duration-200"
>

    {{-- This hidden input is required by Trix to store its value. --}}
    <input id="{{ $field->getName() }}" value="{{ $attributes->wire('model')->value() }}" type="hidden">

    {{-- The Trix editor element itself. --}}
    <trix-editor
        input="{{ $field->getName() }}"
        x-ref="trix"
        class="trix-content block w-full transition-colors duration-200 focus:outline-none sm:text-sm"
        :class="{ 'is-disabled': isViewOnly }"
    ></trix-editor>
</div>
