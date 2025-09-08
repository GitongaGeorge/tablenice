<?php

namespace Mystamyst\TableNice\Columns;

use Illuminate\Database\Eloquent\Model;
use NumberFormatter;

class NumberColumn extends Column
{
    protected $formatCallback = null;

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

    public function formatValue($value, ?Model $model = null)
    {
        if ($this->formatCallback) {
            return call_user_func($this->formatCallback, $value, $model);
        }
        return $value;
    }

    public function toHtml(Model $model): string
    {
        $value = $this->resolveValue($model);
        $formattedValue = $this->formatValue($value, $model);

        $alignmentClass = $this->getAlignmentClass() ?: 'text-right';

        $tooltipAttributes = '';
        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }
        
        $finalClasses = trim(sprintf(
            'px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-slate-200 %s %s',
            $alignmentClass,
            $this->getStickyClasses()
        ));

        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        return sprintf(
            '<td class="%s" %s %s>%s</td>',
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            e($formattedValue)
        );
    }
}
