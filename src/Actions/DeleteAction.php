<?php

namespace Mystamyst\Tablenice\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class DeleteAction extends Action
{
    public function __construct(string $name = 'delete', string $label = 'Delete')
    {
        parent::__construct($name, $label);
        $this->icon('heroicon-o-trash');
        $this->color('danger');
        $this->confirmation('Are you sure you want to delete this record? This action cannot be undone.');
    }

    public function handle(...$records): mixed
    {
        if (empty($records) || !($records[0] instanceof Model)) {
            Session::flash('error', 'No record provided for deletion.');
            return null;
        }

        $record = $records[0];

        if ($this->before instanceof \Closure) {
            ($this->before)($record);
        }

        try {
            $record->delete();
            Session::flash('success', 'Record deleted successfully!');
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            Session::flash('error', 'Error deleting record: ' . $e->getMessage());
        }

        if ($this->after instanceof \Closure) {
            ($this->after)($record);
        }

        return null;
    }
}