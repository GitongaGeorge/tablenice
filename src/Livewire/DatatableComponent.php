<?php

namespace Mystamyst\TableNice\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Mystamyst\TableNice\Actions\Action;
use Mystamyst\TableNice\Actions\BulkAction;
use Mystamyst\TableNice\Actions\PageAction;
use Mystamyst\TableNice\Columns\Column;
use Mystamyst\TableNice\Columns\IndexColumn;
use Mystamyst\TableNice\Columns\RelationColumn;
use Mystamyst\TableNice\Table;
use Mystamyst\TableNice\QueryFilters\Filter;
use Mystamyst\TableNice\QueryFilters\Search;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

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
                $width = $widthString ? (int) filter_var($widthString, FILTER_SANITIZE_NUMBER_INT) : 150;
                $stickyOffset += $width;
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
        $keyName = $this->getKeyName();
        $pageIds = $this->paginatedItems()->getCollection()->pluck($keyName)->map(fn($id) => (string) $id);
        $this->selectAll = $pageIds->isNotEmpty() && $pageIds->every(fn($id) => in_array($id, $this->selectedRows));
    }

    public function updatedSelectAll($value)
    {
        $keyName = $this->getKeyName();
        $pageIds = $this->paginatedItems()->getCollection()->pluck($keyName)->map(fn($id) => (string) $id)->toArray();

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

    protected function buildQuery()
    {
        $query = $this->table->query(); // Use the new query() method

        $relationsToLoad = collect($this->table->columns())
            ->filter(fn($column) => $column instanceof RelationColumn)
            ->map(fn(RelationColumn $column) => $column->getRelationName())
            ->unique()
            ->all();

        if (!empty($relationsToLoad)) {
            $query->with($relationsToLoad);
        }

        $state = app(Pipeline::class)
            ->send([
                'query' => $query,
                'search_term' => $this->search,
                'searchable_columns' => collect($this->table->columns())->filter->isSearchable()->all(),
                'active_filters' => $this->filters,
                'all_columns' => $this->table->columns(),
            ])
            ->through([Search::class, Filter::class])
            ->thenReturn();

        $baseQuery = $state['query'];

        if ($this->sortField && !$this->activeGroupData()) {
            $sortColumn = collect($this->table->columns())->first(fn(Column $c) => $c->getName() === $this->sortField);
            if ($sortColumn) {
                $baseQuery = $sortColumn->sortLogic($baseQuery, $this->sortDirection);
            }
        }

        return $baseQuery;
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
        $action = collect($this->table->actions())->first(fn(Action $action) => $action->getName() === $actionName);
        $model = $this->table->query()->find($modelId);
        
        if ($action && $model) {
            $action->runOnModel($model);
            if ($message = $action->getSuccessMessage()) {
                $this->dispatch('showAlert', message: $message, type: 'success', theme: $this->theme());
            }
            $this->refresh();
        } else {
            $this->dispatch('showAlert', message: 'Error:  Could not find the record to perform the action.', type: 'error', theme: $this->theme());
        }
    }

    #[On('runBulkAction')]
    public function runBulkAction()
    {
        $action = collect($this->table->bulkActions())->first(fn(BulkAction $action) => $action->getName() === $this->activeBulkAction);
        $models = $this->table->query()->whereIn($this->getKeyName(), $this->selectedRows)->get();
        foreach ($models as $model) {
            $action->runOnModel($model);
        }
        if ($message = $action->getSuccessMessage()) {
            $this->dispatch('showAlert', message: $message, type: 'success', theme: $this->theme());
        }
        $this->refresh();
    }

    #[Computed]
    public function paginatedItems(): LengthAwarePaginator
    {
        // Check if a custom data collection is provided
        if (($data = $this->table->data()) !== null) {
            // Manually handle filtering, searching, and sorting for collections
            $collection = $this->processCollection($data);

            return new Paginator(
                $collection->forPage($this->getPage(), $this->perPage),
                $collection->count(),
                $this->perPage,
                $this->getPage()
            );
        }

        // Fallback to Eloquent query builder
        $query = $this->buildQuery();
        if ($this->activeGroupData()) {
            $query->orderBy($this->activeGroupData()->name, $this->activeGroupData()->direction->value);
        }
        return $query->paginate($this->perPage);
    }
    
    protected function processCollection(Collection $collection): Collection
    {
        // Apply search
        if (!empty($this->search)) {
            $searchableColumns = collect($this->table->columns())->filter->isSearchable()->pluck('name')->all();
            $collection = $collection->filter(function ($item) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (Str::contains(strtolower(data_get($item, $column)), strtolower($this->search))) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Apply filters
        if ($this->hasActiveFilters) {
            foreach($this->filters as $field => $value) {
                if (!empty($value)) {
                    $collection = $collection->where($field, $value);
                }
            }
        }
        
        // Apply sorting
        if ($this->sortField) {
            $collection = $this->sortDirection === 'asc'
                ? $collection->sortBy($this->sortField)
                : $collection->sortByDesc($this->sortField);
        }

        return $collection->values();
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
    
    // ... (rest of component is unchanged but included for completeness)
    private function getKeyName(): string
    {
        return $this->table->model ? app($this->table->model)->getKeyName() : 'id';
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

        $allItemsForTotalSummary = ($this->table->data() !== null) ? $this->processCollection($this->table->data()) : $this->buildQuery()->get();

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
        $allItems = ($this->table->data() !== null) ? $this->processCollection($this->table->data()) : $this->buildQuery()->get();
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
}

