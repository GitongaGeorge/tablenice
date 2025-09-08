<?php

namespace Mystamyst\TableNice\Forms\Fields;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class Field
{
    protected string $type;
    protected string $name;
    protected ?string $label = null;
    protected mixed $defaultValue = null;
    protected bool $isRequired = false;
    protected array $validationRules = [];
    protected bool $isDisabled = false;
    protected int $columnSpan = 12;
    protected bool $startsOnNewRow = false;
    /**
     * @var bool|callable
     */
    protected $isVisible = true;

    // This property allows a specific view to be set on a field instance.
    protected ?string $view = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    // START: CORRECTED METHODS
    /**
     * Get the view path for the field. This is the method that was missing.
     * It dynamically determines the Blade component to use for rendering.
     *
     * @return string
     */
    public function getView(): string
    {
        if ($this->view) {
            return $this->view;
        }

        // e.g., 'RadioField' becomes 'radio-field'.
        $viewName = Str::kebab(
            Str::beforeLast(class_basename($this), 'Field')
        );

        return "components.forms.fields.{$viewName}";
    }

    /**
     * Set a custom view path for the field.
     */
    public function view(string $viewPath): static
    {
        $this->view = $viewPath;
        return $this;
    }
    // END: CORRECTED METHODS


    public function permission(string $permissionName): self
    {
        $this->isVisible = fn () => Gate::allows($permissionName);
        return $this;
    }

    public function visible($condition): self
    {
        $this->isVisible = $condition;
        return $this;
    }

    public function isVisible(): bool
    {
        if (is_callable($this->isVisible)) {
            return call_user_func($this->isVisible);
        }
        return $this->isVisible;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? Str::of($this->name)->replace('_', ' ')->title();
    }

    public function label(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function defaultValue(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function required(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function rules(array $rules): static
    {
        $this->validationRules = $rules;
        return $this;
    }

    public function getRules(): array
    {
        $rules = $this->validationRules;
        if ($this->isRequired) {
            if (!in_array('required', $rules)) {
                array_unshift($rules, 'required');
            }
        }
        return $rules;
    }

    public function getValidationMessages(): array
    {
        return [
            'required' => 'The ' . strtolower($this->getLabel()) . ' field is required.',
            'email' => 'Please enter a valid email address.',
            // Add other generic messages here
        ];
    }


    public function disabled(bool $disabled = true): static
    {
        $this->isDisabled = $disabled;
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function columnSpan(int $span): static
    {
        $this->columnSpan = $span;
        return $this;
    }

    public function columnSpanFull(): static
    {
        $this->columnSpan = 12;
        return $this;
    }

    public function getColumnSpan(): int
    {
        return $this->columnSpan;
    }

    public function startsOnNewRow(bool $startsOnNewRow = true): static
    {
        $this->startsOnNewRow = $startsOnNewRow;
        return $this;
    }

    public function shouldStartOnNewRow(): bool
    {
        return $this->startsOnNewRow;
    }
}
