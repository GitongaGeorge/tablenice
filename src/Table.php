<?php

namespace Mystamyst\TableNice;

use Mystamyst\TableNice\Actions\Action;
use Mystamyst\TableNice\Actions\BulkAction;
use Mystamyst\TableNice\Actions\PageAction;
use Mystamyst\TableNice\Columns\Column;
use Mystamyst\TableNice\Enums\Theme;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The core configuration class for a datatable.
 * Extend this class to define the structure and behavior of a specific table.
 */
abstract class Table
{
    /**
     * The Eloquent model class for the table.
     * This is optional if you override the query() or data() methods.
     */
    public ?string $model = null;

    public string $id;

    public function __construct()
    {
        $this->id = Str::kebab(class_basename($this));
    }

    /**
     * Define the base Eloquent query for the table.
     *
     * You can override this method to add your own constraints, joins,
     * or scopes to the query before any searching or filtering is applied.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        if (!$this->model) {
            throw new \Exception('The $model property must be set on the Table class, or the query() method must be overridden.');
        }
        return ($this->model)::query();
    }

    /**
     * Provide a custom data source for the table.
     *
     * Override this method to populate the table from an API, a custom
     * collection, multiple models, etc. If this method returns a collection,
     * it will be used instead of the query() method.
     *
     * Note: For features like bulk actions to work correctly, each item
     * in the collection should be an object with a unique 'id' property.
     *
     * @return Collection|null
     */
    public function data(): ?Collection
    {
        return null;
    }

    abstract public function columns(): array;

    public function actions(): array
    {
        return [];
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function pageActions(): array
    {
        return [];
    }

    public function cards(): array
    {
        return [];
    }

    public function groups(): array
    {
        return [];
    }

    /**
     * Defines the color theme for the table.
     * Override this method in your table class to change the theme.
     */
    public function theme(): Theme
    {
        return Theme::BLUE;
    }

    public function showSearch(): bool
    {
        return true;
    }

    public function showFilters(): bool
    {
        return true;
    }

    public function isStriped(): bool
    {
        return false;
    }

    public function hasStickyHeader(): bool
    {
        return true;
    }

    public function showFiltersButtonLabel(): bool
    {
        return false;
    }

    public function showSettingsButtonLabel(): bool
    {
        return false;
    }

    public function defaultSortField(): ?string
    {
        $firstSortable = collect($this->columns())->first(fn ($c) => $c->isSortable());
        return $firstSortable ? $firstSortable->getName() : 'id';
    }

    public function defaultSortDirection(): string
    {
        return 'desc';
    }
}
