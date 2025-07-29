<?php

namespace Mystamyst\Tablenice\Forms\Fields;

use Closure;

abstract class Field
{
    protected string $name;
    protected string $label;
    protected string $type = 'text'; 
    protected mixed $defaultValue = null;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readOnly = false;
    protected array $rules = [];
    protected array $extraAttributes = [];
    protected string $view; // Blade view path for the field

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $name));
        $this->view = 'tablenice::forms.fields.' . \Illuminate\Support\Str::kebab(class_basename(static::class));
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
        $this->required = $required;
        if ($required) {
            $this->rules[] = 'required';
        } else {
            $this->rules = array_diff($this->rules, ['required']);
        }
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function readOnly(bool $readOnly = true): static
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function rules(array|string $rules): static
    {
        $this->rules = array_merge($this->rules, is_string($rules) ? explode('|', $rules) : $rules);
        return $this;
    }

    public function getRules(): array
    {
        return array_unique($this->rules);
    }

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);
        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    public function getView(): string
    {
        return $this->view;
    }


    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
}