<div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('id')">
                    <div class="flex items-center">
                        ID
                        @if($sortField === 'id')
                            @if($sortDirection === 'asc')
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                </svg>
                            @endif
                        @endif
                    </div>
                </th>
                <!-- Add more columns as needed -->
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Your table rows will go here -->
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    Sample Data
                </td>
            </tr>
        </tbody>
    </table>
</div>