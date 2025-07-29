<?php

namespace Mystamyst\Tablenice\Core;

use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Mystamyst\Tablenice\Contracts\HasActions;
use Mystamyst\Tablenice\Contracts\HasColumns;
use Mystamyst\Tablenice\Core\Concerns\WithActions;
use Mystamyst\Tablenice\Core\Concerns\WithColumns;
use Mystamyst\Tablenice\Core\Concerns\WithFilters;
use Mystamyst\Tablenice\Core\Concerns\WithForms;
use Mystamyst\Tablenice\Core\Concerns\WithPagination;
use Mystamyst\Tablenice\Core\Concerns\WithSearch;
use Mystamyst\Tablenice\Core\Concerns\WithSorting;
use Mystamyst\Tablenice\Core\Concerns\WithStickyColumns;
use Mystamyst\Tablenice\Core\Concerns\WithSummaries;
use Mystamyst\Tablenice\Columns\RelationshipColumn; // Import the RelationshipColumn class

abstract class Datatable extends Component implements HasColumns, HasActions
{
    use WithColumns,
        WithActions,
        WithFilters,
        WithForms,
        WithPagination,
        WithSearch,
        WithSorting,
        WithStickyColumns,
        WithSummaries;

    /**
     * The Livewire layout for the component.
     * @var string
     */
    protected $layout = 'tablenice::layouts.app';

    /**
     * Define the base Eloquent query for the datatable.
     *
     * @return Builder
     */
    abstract public function query(): Builder;

    /**
     * Render the Livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = $this->applyConcerns($this->query());

        // Eager loading for relationships defined in columns
        $query = $this->applyEagerLoading($query);

        $data = $query->paginate($this->getPerPage());

        return \view('tablenice::components.datatable.table', [
            'data' => $data,
            'columns' => $this->getColumns(),
            'actions' => $this->getActions(),
            'summaries' => $this->getSummaries(),
        ])->layout($this->layout);
    }

    /**
     * Apply eager loading based on relationship columns.
     */
    protected function applyEagerLoading(Builder $query): Builder
    {
        $eagerLoads = collect($this->getColumns())
            // Filter to ensure we only get instances of RelationshipColumn
            ->filter(fn ($column) => $column instanceof RelationshipColumn)
            // Now, in the map, we can safely type-hint $column as RelationshipColumn
            ->map(fn (RelationshipColumn $column) => $column->getRelationship())
            ->flatten()
            ->unique()
            ->toArray();

        return $query->with($eagerLoads);
    }

    /**
     * Apply all concerns to the query.
     */
    protected function applyConcerns(Builder $query): Builder
    {
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query);
        $query = $this->applySorting($query);
        // WithForms is for modals, etc., not direct query manipulation.
        // WithActions is for UI, not query manipulation.
        // WithColumns defines columns, not query.
        // WithSummaries works on the results, not query.
        // WithStickyColumns is for UI.
        return $query;
    }
}