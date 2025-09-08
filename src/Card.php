<?php

namespace Mystamyst\TableNice;

use Mystamyst\TableNice\Enums\ChartType;
use Mystamyst\TableNice\Summaries\Contracts\Summary;
use Illuminate\Support\Collection;
use NumberFormatter;

class Card
{
    public $value;
    protected $formatCallback = null;
    protected $chartDataResolver = null;
    public ?ChartType $chartType = null;
    public ?array $chartColors = null;
    protected $chartTooltipFormatter = null;

    public function __construct(
        public string $title,
        $value,
        public ?string $columnName = null,
        public ?string $subtitle = null,
        public string $titleColor = 'text-gray-500 dark:text-gray-400',
        public string $valueColor = 'text-gray-900 dark:text-gray-100',
        public string $subtitleColor = 'text-green-600 dark:text-green-400'
    ) {
        $this->value = $value;
    }

    public static function make(
        string $title,
        $value,
        ?string $columnName = null,
        ?string $subtitle = null,
        string $titleColor = 'text-gray-500 dark:text-gray-400',
        string $valueColor = 'text-gray-900 dark:text-gray-100',
        string $subtitleColor = 'text-green-600 dark:text-green-400'
    ): static {
        return new static($title, $value, $columnName, $subtitle, $titleColor, $valueColor, $subtitleColor);
    }

    public function format(callable $callback): self
    {
        $this->formatCallback = $callback;
        return $this;
    }

    public function currency(string $currency = 'USD', string $locale = 'en_US'): self
    {
        return $this->format(function ($value) use ($currency, $locale) {
            if (!is_numeric($value)) return $value;
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($value, $currency);
        });
    }

    public function decimal(int $decimals = 2): self
    {
        return $this->format(function ($value) use ($decimals) {
            if (!is_numeric($value)) return $value;
            return number_format($value, $decimals);
        });
    }

    public function withChart(array|callable $data, ChartType $type = ChartType::BAR, ?array $colors = null): self
    {
        $this->chartDataResolver = $data;
        $this->chartType = $type;
        $this->chartColors = $colors;
        $this->chartTooltipFormatter = fn ($value, $label) => "{$label}: {$value}";
        return $this;
    }

    public function chartTooltip(callable $formatter): self
    {
        $this->chartTooltipFormatter = $formatter;
        return $this;
    }

    public function hasChart(): bool
    {
        return !is_null($this->chartDataResolver);
    }

    public function getChartConfig(Collection $items): ?array
    {
        if (!$this->hasChart()) {
            return null;
        }

        $data = is_callable($this->chartDataResolver)
            ? call_user_func($this->chartDataResolver, $items)
            : $this->chartDataResolver;

        // Convert collection to array to prevent type error
        if ($data instanceof Collection) {
            $data = $data->all();
        }

        $labels = array_keys($data);
        $values = array_values($data);

        return [
            'type' => $this->chartType->value,
            'labels' => $labels,
            'values' => $values,
            'colors' => $this->chartColors,
            'tooltips' => array_map($this->chartTooltipFormatter, $values, $labels),
        ];
    }

    public function resolveValue(Collection $items): string
    {
        $val = $this->value;
        $rawValue = null;

        if ($val instanceof Summary) {
            if (is_null($this->columnName)) {
                throw new \Exception('A column name must be provided when using a Summary class for a card value.');
            }
            $rawValue = $val->calculate($items, $this->columnName);
        } elseif (is_callable($val)) {
            $rawValue = $val($items);
        } else {
            $rawValue = $val;
        }

        if ($this->formatCallback) {
            return call_user_func($this->formatCallback, $rawValue);
        }

        return is_numeric($rawValue) ? number_format($rawValue) : (string) $rawValue;
    }
}
