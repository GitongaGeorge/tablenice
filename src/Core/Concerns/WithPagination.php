<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Livewire\WithPagination as LivewireWithPagination;

trait WithPagination
{
    use LivewireWithPagination;

    public int $perPage;

    public function mountWithPagination()
    {
        $this->perPage = config('tablenice.pagination.default_per_page', 10);
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage(); // Reset to first page when per page changes
    }

    public function paginationView(): string
    {
        return config('tablenice.pagination.view', 'tablenice::pagination.tablenice');
    }
}