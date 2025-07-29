<?php

namespace Mystamyst\Tablenice\Actions;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class BulkDeleteAction extends Action
{
    protected ?string $modelClass = null; // The model class for bulk operations

    public function __construct(string $name = 'bulk_delete', string $label = 'Bulk Delete')
    {
        parent::__construct($name, $label);
        $this->icon('heroicon-o-trash');
        $this->color('danger');
        $this->confirmation('Are you sure you want to delete selected records? This action cannot be undone.');
    }

    public function model(string $modelClass): static
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    public function handle(...$recordIds): mixed
    {
        if (empty($recordIds) || !$this->modelClass) {
            Session::flash('error', 'No records selected or model class not defined for bulk deletion.');
            return null;
        }

        if ($this->before instanceof \Closure) {
            ($this->before)(collect($recordIds));
        }

        try {
            $this->modelClass::whereIn('id', $recordIds)->delete();
            Session::flash('success', count($recordIds) . ' records deleted successfully!');
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            Session::flash('error', 'Error deleting records: ' . $e->getMessage());
        }

        if ($this->after instanceof \Closure) {
            ($this->after)(collect($recordIds));
        }

        return null;
    }

    /**
     * This method is called by WithActions trait for bulk actions.
     * @param Collection $selectedRecordIds
     */
    public function handleBulk(Collection $selectedRecordIds)
    {
        $this->handle(...$selectedRecordIds->toArray());
    }
}