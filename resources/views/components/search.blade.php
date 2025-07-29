<div class="mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 max-w-md">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="{{ config('tablenice.search_placeholder') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>
        
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Show:</label>
            <select 
                wire:model.live="perPage" 
                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
                @foreach(config('tablenice.per_page_options') as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>