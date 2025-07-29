<div>
    {{-- Search and Filters --}}
    <div class="mb-4 p-4 bg-white rounded-lg shadow">
        <div class="flex items-center space-x-4">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search..."
                class="form-input rounded-md shadow-sm mt-1 block w-full"
            >
            {{-- Render filters if you have them --}}
            @include('tablenice::components.datatable.filters')
        </div>
    </div>

    {{-- Main Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if(count($actions) > 0 && collect($actions)->contains(fn($action) => $action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" wire:model.live="selectAll" />
                        </th>
                    @endif
                    @foreach($columns as $column)
                        @if(!$column->isHidden())
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider
                                    {{ $column->isSortable() ? 'cursor-pointer' : '' }}"
                                @if($column->isSortable()) wire:click="sortBy('{{ $column->getName() }}')" @endif
                            >
                                <div class="flex items-center">
                                    {{ $column->getLabel() }}
                                    @if ($sortColumn === $column->getName())
                                        <span class="ml-1">
                                            @if ($sortDirection === 'asc')
                                                &#x25B2; {{-- Up arrow --}}
                                            @else
                                                &#x25BC; {{-- Down arrow --}}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                        @endif
                    @endforeach
                    @if(count($actions) > 0)
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $index => $record)
                    <tr wire:key="{{ $record->getKey() }}">
                        @if(count($actions) > 0 && collect($actions)->contains(fn($action) => $action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <input type="checkbox" wire:model.live="selectedRows" value="{{ $record->getKey() }}" />
                            </td>
                        @endif
                        @foreach($columns as $column)
                            @if(!$column->isHidden())
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-{{ $column->getTextAlign() }}">
                                    @if($column instanceof \Mystamyst\Tablenice\Columns\IndexColumn)
                                        {{ $data->firstItem() + $index }}
                                    @else
                                        {!! $column->render($record) !!}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        @if(count($actions) > 0)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @foreach($actions as $action)
                                        @if($action->canRun($record) && !($action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction))
                                            <x-tablenice-button
                                                :label="$action->getLabel()"
                                                :wire-click="$action->getConfirmation() ? 'confirmAction(\'' . $action->getName() . '\', ' . $record->getKey() . ')' : 'callAction(\'' . $action->getName() . '\', ' . $record->getKey() . ')'"
                                                :color="$action->getColor()"
                                                :icon="$action->getIcon()"
                                                :style="$action->getStyle()"
                                                :loading-target="'callAction(\'' . $action->getName() . '\', ' . $record->getKey() . ')'"
                                            />
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) + (collect($actions)->contains(fn($action) => $action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction) ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($summaries) > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        @if(count($actions) > 0 && collect($actions)->contains(fn($action) => $action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction))
                            <td></td> {{-- For checkbox column --}}
                        @endif
                        @foreach($columns as $column)
                            @if(!$column->isHidden())
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @php
                                        $summary = collect($summaries)->firstWhere('column', $column->getAttribute());
                                    @endphp
                                    @if($summary)
                                        {{ $summary->getLabel() }}: <strong>{{ $this->calculateSummaries()[$summary->getName()] ?? 'N/A' }}</strong>
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        @if(count($actions) > 0)
                            <td></td> {{-- For actions column --}}
                        @endif
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Pagination and Bulk Actions --}}
    <div class="mt-4 p-4 bg-white rounded-lg shadow flex items-center justify-between">
        @if (!empty($selectedRows) && collect($actions)->contains(fn($action) => $action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction))
            <div>
                <span class="text-sm text-gray-700">{{ count($selectedRows) }} selected</span>
                @foreach ($actions as $action)
                    @if ($action instanceof \Mystamyst\Tablenice\Actions\BulkDeleteAction)
                        <x-tablenice-button
                            :label="$action->getLabel()"
                            :wire-click="$action->getConfirmation() ? 'confirmAction(\'' . $action->getName() . '\')' : 'callAction(\'' . $action->getName() . '\')'"
                            :color="$action->getColor()"
                            class="ml-2"
                        />
                    @endif
                @endforeach
            </div>
        @endif

        <div class="flex-1">
            {{ $data->links('tablenice::pagination.tablenice') }}
        </div>

        <div class="ml-4 flex items-center space-x-2">
            <label for="perPage" class="text-sm text-gray-700">Per Page:</label>
            <select wire:model.live="perPage" id="perPage" class="form-select rounded-md shadow-sm text-sm">
                @foreach(config('tablenice.pagination.per_page_options') as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Modals (for forms and confirmations) --}}
    @livewire('tablenice::forms.modal', ['show' => $showFormModal, 'title' => $modalTitle])
        @if ($showFormModal && $modalFormClass)
            <form wire:submit.prevent="saveForm">
                @foreach ($modalFormFields as $field)
                    @include($field->getView(), ['field' => $field, 'model' => $modalRecord, 'value' => $formData[$field->getName()] ?? $field->getDefaultValue()])
                @endforeach

                <div class="mt-4 flex justify-end space-x-2">
                    <x-tablenice-button label="Cancel" wire-click="closeFormModal" color="secondary" />
                    <x-tablenice-button label="Save" type="submit" color="primary" loading-target="saveForm" />
                </div>
            </form>
        @endif
    </x-tablenice-modal>

    {{-- Confirmation Modal (separate from form modal for reusability) --}}
    <x-tablenice-modal wire:model="showConfirmationModal" title="Confirm Action">
        <p>{{ $confirmationMessage }}</p>
        <div class="mt-4 flex justify-end space-x-2">
            <x-tablenice-button label="Cancel" wire-click="cancelConfirmation" color="secondary" />
            <x-tablenice-button label="Confirm" wire-click="executeConfirmedAction" color="danger" />
        </div>
    </x-tablenice-modal>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('openFormModal', (formClass, recordId) => {
                @this.call('openFormModal', formClass, recordId);
            });
            @this.on('refreshDatatable', () => {
                // No specific action needed, Livewire takes care of reactive updates
                // But you can add custom JS here if needed for UI refresh.
            });
            @this.on('confirmAction', (actionName, recordId, confirmationMessage) => {
                @this.set('confirmationMessage', confirmationMessage);
                @this.set('actionToConfirm', actionName);
                @this.set('recordIdToConfirm', recordId);
                @this.set('showConfirmationModal', true);
            });
        });
    </script>
</div>