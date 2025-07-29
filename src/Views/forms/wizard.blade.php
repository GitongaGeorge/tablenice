<div x-data="{ currentStep: @entangle('currentStep').live }">
    <nav class="flex justify-center mb-6" aria-label="Progress">
        <ol role="list" class="space-x-4 flex">
            @foreach ($steps as $index => $step)
                <li>
                    @if ($index + 1 < $currentStep)
                        <button type="button" wire:click="goToStep({{ $index + 1 }})" class="group flex items-center">
                            <span class="flex items-center px-2 py-2 text-sm font-medium">
                                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
                                    <x-heroicon-s-check class="w-5 h-5 text-white" />
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ $step }}</span>
                            </span>
                        </button>
                    @elseif ($index + 1 === $currentStep)
                        <span class="flex items-center px-2 py-2 text-sm font-medium" aria-current="step">
                            <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center border-2 border-indigo-600 rounded-full">
                                <span class="text-indigo-600">{{ $index + 1 }}</span>
                            </span>
                            <span class="ml-3 text-sm font-medium text-indigo-600">{{ $step }}</span>
                        </span>
                    @else
                        <button type="button" wire:click="goToStep({{ $index + 1 }})" class="group flex items-center">
                            <span class="flex items-center px-2 py-2 text-sm font-medium">
                                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                                    <span class="text-gray-500 group-hover:text-gray-900">{{ $index + 1 }}</span>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">{{ $step }}</span>
                            </span>
                        </button>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="py-4">
        {{ $slot }}
    </div>

    <div class="mt-6 flex justify-between">
        @if ($currentStep > 1)
            <x-tablenice-button label="Previous" wire-click="prevStep" color="secondary" />
        @else
            <div></div> {{-- Empty div for spacing --}}
        @endif

        @if ($currentStep < count($steps))
            <x-tablenice-button label="Next" wire-click="nextStep" color="primary" />
        @else
            <x-tablenice-button label="Submit" type="submit" color="success" />
        @endif
    </div>
</div>