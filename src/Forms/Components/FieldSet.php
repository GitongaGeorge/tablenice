<?php

namespace Mystamyst\TableNice\Forms\Components;

use Illuminate\Support\Facades\Gate;

class FieldSet
{
    protected ?string $label = null;
    protected array $schema = [];
    /**
     * @var bool|callable
     */
    protected $isVisible = true;

    public static function make(): static
    {
        return new static();
    }
    
    /**
     * Set the permission required to see this fieldset.
     */
    public function permission(string $permissionName): self
    {
        $this->isVisible = fn () => Gate::allows($permissionName);
        return $this;
    }

    /**
     * Set the visibility of the fieldset.
     */
    public function visible($condition): self
    {
        $this->isVisible = $condition;
        return $this;
    }

    /**
     * Determine if the fieldset is currently visible.
     */
    public function isVisible(): bool
    {
        if (is_callable($this->isVisible)) {
            return call_user_func($this->isVisible);
        }

        return $this->isVisible;
    }

    public function label(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function schema(array $fields): static
    {
        $this->schema = $fields;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSchema(): array
    {
        return $this->schema;
    }
}
