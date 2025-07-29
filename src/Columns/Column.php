<?php

namespace Mystamyst\Tablenice\Columns;

use Closure;
use Illuminate\Database\Eloquent\Model;

abstract class Column
{
    protected string $name;
    protected string $label;
    protected ?string $attribute = null; // The model attribute to display
    protected ?Closure $formatUsing = null; // Custom formatting callback
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected ?string $sortAttribute = null; // Custom attribute for sorting if different from display attribute
    protected bool $hidden = false; // For column visibility toggling
    protected string $textAlign = 'left'; // left, center, right
    protected array $classes = []; // Custom CSS classes for the column cell
    protected ?string $view = null; // Custom Blade view for rendering the column

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $name));
        $this->attribute = $name; // Default to name
    }

    public static function make(string $name, ?string $label = null): static
    {
        return new static($name, $label);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function getSortAttribute(): ?string
    {
        return $this->sortAttribute;
    }

    public function sortable(bool $sortable = true, ?string $attribute = null): static
    {
        $this->sortable = $sortable;
        $this->sortAttribute = $attribute;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;
        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function hidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function formatUsing(Closure $callback): static
    {
        $this->formatUsing = $callback;
        return $this;
    }

    public function align(string $textAlign): static
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    public function getTextAlign(): string
    {
        return $this->textAlign;
    }

    public function classes(array $classes): static
    {
        $this->classes = $classes;
        return $this;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function view(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * Resolve the column value for a given record.
     *
     * @param Model $record
     * @return mixed
     */
    public function getValue(Model $record): mixed
    {
        $value = data_get($record, $this->attribute);

        if ($this->formatUsing instanceof Closure) {
            return ($this->formatUsing)($value, $record);
        }

        return $value;
    }

    /**
     * Render the column's value. Can be overridden by specific column types.
     *
     * @param Model $record
     * @return string|\Illuminate\Contracts\View\View
     */
    public function render(Model $record)
    {
        if ($this->view) {
            return \view($this->view, ['column' => $this, 'record' => $record, 'value' => $this->getValue($record)]);
        }
        return $this->getValue($record);
    }
}