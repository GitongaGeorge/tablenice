<?php

namespace Mystamyst\Tablenice\Components;

use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc']
    ];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function render()
    {
        return view('tablenice::datatable');
    }
}