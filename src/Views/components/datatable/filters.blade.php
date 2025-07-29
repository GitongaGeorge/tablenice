{{-- A simple example. You'd build out more complex filters. --}}
<div> 
    <select wire:model.live="filters.status" class="form-select rounded-md shadow-sm mt-1 block w-full">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select> 
</div>