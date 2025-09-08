<?php

namespace Mystamyst\TableNice\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DateTimeColumn extends Column
{
    protected string $format = '';
    protected bool $isHumanReadable = false;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label, 'datetime');
    }

    public function format(string $format): self
    {
        $this->format = $format;
        $this->isHumanReadable = false;
        return $this;
    }

    public function day(string $format = 'd'): self
    {
        $this->format .= ' ' . $format;
        return $this;
    }

    public function month(string $format = 'M'): self
    {
        $this->format .= ' ' . $format;
        return $this;
    }

    public function year(string $format = 'Y'): self
    {
        $this->format .= ' ' . $format;
        return $this;
    }

    public function time(string $format = 'H:i:s'): self
    {
        $this->format .= ' ' . $format;
        return $this;
    }

    public function since(): self
    {
        $this->isHumanReadable = true;
        return $this;
    }

    public function toHtml(Model $model): string
    {
        $value = $this->resolveValue($model);

        if (empty($value)) {
            return '<td></td>';
        }

        $carbon = Carbon::parse($value);

        $displayValue = $this->isHumanReadable
            ? $carbon->diffForHumans()
            : $carbon->format(trim($this->format));

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
            'px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400 %s %s',
            $this->getAlignmentClass(),
            $this->getStickyClasses()
        ));
        
        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        return sprintf(
            '<td class="%s" %s %s>%s</td>',
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            e($displayValue)
        );
    }
}
