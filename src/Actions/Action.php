<?php

namespace Mystamyst\Tablenice\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Action
{
    protected string $name;
    protected string $label;
    protected ?string $icon = null;
    protected string $color = 'primary'; // e.g., 'primary', 'secondary', 'danger', 'success', 'warning'
    protected string $style = 'button'; // 'button', 'link'
    protected Closure|bool $canRun = true; // Closure to determine if action can be run for a record
    protected ?string $modal = null; // FQCN of a Livewire modal component to open
    protected ?array $modalParams = []; // Parameters to pass to the modal
    protected ?Closure $before = null;
    protected ?Closure $after = null;
    protected ?string $confirmation = null; // Confirmation message for the action

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public static function make(string $name, string $label): static
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

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function style(string $style): static
    {
        $this->style = $style;
        return $this;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function can(Closure|bool $callback): static
    {
        $this->canRun = $callback;
        return $this;
    }

    public function canRun(?Model $record = null): bool
    {
        if (is_bool($this->canRun)) {
            return $this->canRun;
        }
        return ($this->canRun)($record);
    }

    public function modal(string $modalClass, array $params = []): static
    {
        $this->modal = $modalClass;
        $this->modalParams = $params;
        return $this;
    }

    public function getModal(): ?string
    {
        return $this->modal;
    }

    public function getModalParams(?Model $record = null): array
    {
        if ($record && in_array('{record_id}', $this->modalParams)) {
            $params = $this->modalParams;
            $index = array_search('{record_id}', $params);
            if ($index !== false) {
                $params[$index] = $record->getKey();
            }
            return $params;
        }
        return $this->modalParams;
    }

    public function before(Closure $callback): static
    {
        $this->before = $callback;
        return $this;
    }

    public function after(Closure $callback): static
    {
        $this->after = $callback;
        return $this;
    }

    public function confirmation(string $message): static
    {
        $this->confirmation = $message;
        return $this;
    }

    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    
    /**
     * Handle a bulk action.
     *
     * @param Collection $selectedRows
     * @return void
     */
    public function handleBulk(Collection $selectedRows)
    {
        // Implement bulk action logic here or throw exception if not supported
        throw new \BadMethodCallException('Bulk action not implemented for this action.');
    }

    /**
     * Handle the action logic.
     * @param Model ...$records
     * @return mixed
     */
    abstract public function handle(...$records): mixed;
}