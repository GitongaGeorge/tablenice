<?php

namespace Mystamyst\TableNice\Livewire;

use Mystamyst\TableNice\Actions\Action;
use Mystamyst\TableNice\Actions\BulkAction;
use Mystamyst\TableNice\Actions\PageAction;
use Mystamyst\TableNice\Columns\Column;
use Mystamyst\TableNice\Columns\IndexColumn;
use Mystamyst\TableNice\Columns\RelationColumn;
use Mystamyst\TableNice\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DatatableComponent extends Component
{
    use WithPagination;

    public string $tableClass;

    // State for the table
    public int $perPage;
    public string $search = '';
    public array $filters = [];
    public ?string $sortField = null;
    public string $sortDirection = 'asc';
    public array $selectedRows = [];
    public bool $selectAll = false;
    public ?string $activeBulkAction = null;
    public bool $stickyHeader;

    // State for Groups, Cards, and Summaries
    public ?string $activeGroup = null;
    public array $expandedGroups = [];
    public int $perGroup = 10;

    public function mount()
    {
        $this->perPage = config('tablenice.pagination.default_per_page', 10);
        $this->sortField = $this->table->defaultSortField();
        $this->sortDirection = $this->table->defaultSortDirection();
        $this->initializeFilters();
        $this->stickyHeader = $this->table->hasStickyHeader();

        $defaultGroup = collect($this->table->columns())->first(fn(Column $column) => $column->isDefaultGroup());
        if ($defaultGroup && !$this->activeGroup) {
            $this->activeGroup = $defaultGroup->getName();
        }
    }

    #[Computed]
    public function table(): Table
    {
        return app($this->tableClass);
    }

    #[Computed]
    public function theme(): array
    {
        return $this->table()->theme()->classes();
    }

    #[Computed]
    public function columnsForView(): array
    {
        $theme = $this->theme();
        $columns = collect($this->table->columns())->filter->isVisible()->values();
        $stickyOffset = 60; // Initial offset for the checkbox column

        foreach ($columns as $column) {
            $column->theme = $theme;
            $column->setSearchTerm($this->search);

            if ($column->isSticky()) {
                $column->setStickyOffset($stickyOffset);
                $widthString = $column->getWidth();
                // A simple regex to extract the numeric part of the width (e.g., '150px' -> 150)
                preg_match('/^\d+/', $widthString, $matches);
                $width = $matches[0] ?? 150;
                $stickyOffset += (int) $width;
            }
        }
        return $columns->all();
    }

    #[Computed]
    public function hasFilterableColumns(): bool
    {
        return collect($this->table->columns())->some(fn(Column $column) => $column->isFilterable());
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return collect($this->filters)->filter(fn ($value) => $value !== '' && $value !== null)->isNotEmpty();
    }

    public function initializeFilters()
    {
        foreach ($this->table->columns() as $column) {
            if ($column->isFilterable()) {
                $this->filters[$column->getName()] = '';
            }
        }
    }

    public function clearFilters()
    {
        $this->initializeFilters();
        $this->resetPage();
    }

    public function clearSelectedRows()
    {
        $this->selectedRows = [];
        $this->selectAll = false;
    }

    #[On('refreshDatatable')]
    public function refresh()
    {
        $this->selectedRows = [];
        $this->selectAll = false;
        $this->activeBulkAction = null;
    }

    public function updatedSelectedRows()
    {
        $pageIds = $this->paginatedItems()->pluck($this->getKeyName())->map(fn($id) => (string) $id);
        $this->selectAll = $pageIds->isNotEmpty() && $pageIds->every(fn($id) => in_array($id, $this->selectedRows));
    }

    public function updatedSelectAll($value)
    {
        $pageIds = $this->paginatedItems()->pluck($this->getKeyName())->map(fn($id) => (string) $id)->toArray();
        if ($value) {
            $this->selectedRows = array_unique(array_merge($this->selectedRows, $pageIds));
        } else {
            $this->selectedRows = array_diff($this->selectedRows, $pageIds);
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilters() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingActiveGroup() { $this->resetPage(); }

    public function toggleGroup(string $groupName)
    {
        if (in_array($groupName, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupName]);
        } else {
            $this->expandedGroups[] = $groupName;
        }
    }

    #[Computed]
    public function allItems(): Collection
    {
        // If a data collection is provided, use it directly.
        if (!is_null($data = $this->table()->data())) {
            return $data;
        }

        // Otherwise, build and execute the Eloquent query.
        $query = $this->table()->query();

        $relationsToLoad = collect($this->table->columns())
            ->filter(fn($column) => $column instanceof RelationColumn)
            ->map(fn(RelationColumn $column) => $column->getRelationName())
            ->unique()
            ->all();

        if (!empty($relationsToLoad)) {
            $query->with($relationsToLoad);
        }

        return $query->get();
    }

    #[Computed]
    public function filteredAndSortedItems(): Collection
    {
        $items = $this->allItems();

        // Apply Search
        if ($this->search) {
            $searchableColumns = collect($this->table->columns())->filter->isSearchable();
            $items = $items->filter(function ($item) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (Str::contains(strtolower(data_get($item, $column->getName())), strtolower($this->search))) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Apply Filters
        $activeFilters = collect($this->filters)->filter(fn ($value) => $value !== '' && $value !== null);
        if ($activeFilters->isNotEmpty()) {
            foreach ($activeFilters as $columnName => $value) {
                $items = $items->where($columnName, $value);
            }
        }

        // Apply Sorting
        if ($this->sortField) {
            $items = $this->sortDirection === 'asc'
                ? $items->sortBy($this->sortField)
                : $items->sortByDesc($this->sortField);
        }

        return $items;
    }


    public function handlePageAction(string $actionName)
    {
        $action = collect($this->table->pageActions())->first(fn(PageAction $action) => $action->getName() === $actionName);
        if (!$action) return;

        if ($action->getForm()) {
            $this->dispatch('showFormModal', [
                'component' => 'tablenice-action-form',
                'params' => ['datatableClass' => $this->tableClass, 'actionName' => $action->getName(), 'modelId' => null, 'theme' => $this->theme()],
                'title' => $action->getLabel(), 'size' => $action->getModalSize(),
            ]);
        } else {
            $this->runPageAction($actionName);
        }
    }

    public function handleAction(string $actionName, $modelId = null)
    {
        $action = collect($this->table->actions())->first(fn(Action $action) => $action->getName() === $actionName);

        if (!$action) {
            $pageAction = collect($this->table->pageActions())->first(fn(PageAction $a) => $a->getName() === $actionName);
            if ($pageAction) {
                $this->handlePageAction($actionName);
            }
            return;
        }

        if ($action->getRequiresConfirmation()) {
            $this->dispatch('showConfirmation', [
                'title' => 'Confirm Action', 'message' => 'Are you sure?',
                'confirmButtonText' => 'Yes, ' . $action->getLabel(), 'method' => 'runAction',
                'params' => [$actionName, $modelId], 'theme' => $this->theme(),
            ]);
        } elseif ($action->getForm()) {
            $this->dispatch('showFormModal', [
                'component' => 'tablenice-action-form',
                'params' => ['datatableClass' => $this->tableClass, 'actionName' => $action->getName(), 'modelId' => $modelId, 'theme' => $this->theme()],
                'title' => $action->getLabel(), 'size' => $action->getModalSize(),
            ]);
        } else {
            $this->runAction($actionName, $modelId);
        }
    }

    public function handleBulkAction()
    {
        if (!$this->activeBulkAction || empty($this->selectedRows)) return;
        $action = collect($this->table->bulkActions())->first(fn(BulkAction $action) => $action->getName() === $this->activeBulkAction);
        if (!$action) return;

        if ($action->getRequiresConfirmation()) {
            $this->dispatch('showConfirmation', [
                'title' => 'Confirm Bulk Action', 'message' => 'Perform this action on ' . count($this->selectedRows) . ' records?',
                'confirmButtonText' => 'Yes, ' . $action->getLabel(), 'method' => 'runBulkAction', 'theme' => $this->theme(),
            ]);
        } else {
            $this->runBulkAction();
        }
    }

    #[On('runAction')]
    public function runAction(string $actionName, $modelId)
    {
        $model = $this->findItem($modelId);
        $action = collect($this->table->actions())->first(fn(Action $action) => $action->getName() === $actionName);

        if ($action && $model) {
            $action->runOnModel($model);
            if ($message = $action->getSuccessMessage()) {
                $this->dispatch('showAlert', message: $message, type: 'success', theme: $this->theme());
            }
            $this->refresh();
        } else {
            $this->dispatch('showAlert', message: 'Error: Could not find the record to perform the action.', type: 'error', theme: $this->theme());
        }
    }

    #[On('runBulkAction')]
    public function runBulkAction()
    {
        $action = collect($this->table->bulkActions())->first(fn(BulkAction $action) => $action->getName() === $this->activeBulkAction);

        if (!$this->table()->model) {
             $this->dispatch('showAlert', message: 'Error: Bulk actions are only supported for Eloquent models.', type: 'error', theme: $this->theme());
             return;
        }

        $models = ($this->table()->model)::whereIn(($this->table()->model)::make()->getKeyName(), $this->selectedRows)->get();
        foreach ($models as $model) {
            $action->runOnModel($model);
        }
        if ($message = $action->getSuccessMessage()) {
            $this->dispatch('showAlert', message: $message, type: 'success', theme: $this->theme());
        }
        $this->refresh();
    }

    #[Computed]
    public function availableGroups(): array
    {
        $columnGroups = collect($this->table->columns())->filter->isGroupable()->mapWithKeys(fn(Column $column) => [$column->getName() => $column->getGroup()])->all();
        $definedGroups = collect($this->table->groups())->mapWithKeys(fn($group) => [$group->name => $group])->all();
        return array_merge($columnGroups, $definedGroups);
    }

    #[Computed]
    public function activeGroupData()
    {
        if (!$this->activeGroup) return null;
        return $this->availableGroups()[$this->activeGroup] ?? null;
    }

    #[Computed]
    public function paginatedItems()
    {
        $items = $this->filteredAndSortedItems();

        $page = $this->getPage();
        $perPage = $this->perPage;

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }


    #[Computed]
    public function groupedItems()
    {
        if (!$this->activeGroupData()) return null;

        $itemsOnPage = $this->paginatedItems()->getCollection();
        $grouped = $itemsOnPage->groupBy($this->getGroupingLogic());

        if ($this->sortField) {
            $grouped = $grouped->map(fn($group) => $this->sortDirection === 'asc' ? $group->sortBy($this->sortField) : $group->sortByDesc($this->sortField));
        }
        return $grouped;
    }

    #[Computed]
    public function summaries()
    {
        $summaries = ['total' => [], 'groups' => []];
        $summaryColumns = collect($this->table->columns())->filter->hasSummary();
        if ($summaryColumns->isEmpty()) return $summaries;

        $allItemsForTotalSummary = $this->filteredAndSortedItems();

        foreach ($summaryColumns as $column) {
            $summaries['total'][$column->getName()] = $this->calculateColumnSummary($column, $allItemsForTotalSummary, 'total');
        }

        if ($this->groupedItems() && $this->activeGroupData()) {
            $allGroupedItems = $allItemsForTotalSummary->groupBy($this->getGroupingLogic());
            foreach ($this->groupedItems() as $groupName => $itemsOnPage) {
                $completeGroupItems = $allGroupedItems->get($groupName, collect());
                foreach ($summaryColumns as $column) {
                    $summaries['groups'][$groupName][$column->getName()] = $this->calculateColumnSummary($column, $completeGroupItems, 'group');
                }
            }
        }
        return $summaries;
    }

    #[Computed]
    public function summaryCards()
    {
        $cards = [];
        $allItems = $this->filteredAndSortedItems();
        foreach ($this->table->cards() as $card) {
            $cards[] = [
                'title' => $card->title,
                'subtitle' => $card->subtitle,
                'value' => $card->resolveValue($allItems),
                'chartConfig' => $card->getChartConfig($allItems),
                'titleColor' => $card->titleColor,
                'valueColor' => $card->valueColor,
                'subtitleColor' => $card->subtitleColor,
            ];
        }
        return $cards;
    }

    private function findItem($id)
    {
        return $this->allItems()->first(fn ($item) => data_get($item, $this->getKeyName()) == $id);
    }

    private function getKeyName(): string
    {
        return $this->table()->model ? ($this->table()->model)::make()->getKeyName() : 'id';
    }


    private function getGroupingLogic()
    {
        $activeGroup = $this->activeGroupData();
        if ($activeGroup->format) {
            return fn($item) => empty(data_get($item, $activeGroup->name)) ? 'N/A' : Carbon::parse(data_get($item, $activeGroup->name))->format($activeGroup->format);
        }
        return $activeGroup->name;
    }

    private function calculateColumnSummary(Column $column, Collection $items, string $type)
    {
        $summaryConfig = $column->getSummary();
        $calculator = $summaryConfig['calculator'];
        $labelFormat = $summaryConfig[$type . '_label'] ?? '{value}';
        $rawValue = is_callable($calculator) ? $calculator($items) : $calculator->calculate($items, $column->getName());
        $formattedValue = $rawValue;
        if (method_exists($column, 'formatValue')) {
            $formattedValue = $column->formatValue($rawValue);
        } elseif (is_numeric($rawValue)) {
            $formattedValue = number_format($rawValue);
        }
        return str_replace('{value}', $formattedValue, $labelFormat);
    }

    public function render()
    {
        $items = $this->activeGroupData() ? null : $this->paginatedItems();
        $groupedItems = $this->groupedItems();

        if($items) {
            foreach($this->columnsForView as $column) {
                if ($column instanceof IndexColumn) {
                    $column->paginated($items->currentPage(), $items->perPage());
                }
            }
        }

        return view('tablenice::livewire.datatable-component', [
            'items' => $items,
            'groupedItems' => $groupedItems,
        ]);
    }
}

