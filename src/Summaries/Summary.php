<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

abstract class Summary
{
    protected string $name;
    protected string $label;
    protected string $column; // The column to summarize
    protected int $decimals = 0;
    protected string $prefix = '';
    protected string $suffix = '';

    public function __construct(string $name, string $label, string $column)
    {
        $this->name = $name;
        $this->label = $label;
        $this->column = $column;
    }

    public static function make(string $name, string $label, string $column): static
    {
        return new static($name, $label, $column);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->suffix = $suffix;
        return $this;
    }

    protected function formatValue(mixed $value): string
    {
        if (is_numeric($value)) {
            return $this->prefix . number_format($value, $this->decimals) . $this->suffix;
        }
        return (string) $value;
    }

    /**
     * Calculate the summary value.
     *
     * @param Builder $query The query before pagination.
     * @return mixed
     */
    abstract public function calculate(Builder $query): mixed;
}a