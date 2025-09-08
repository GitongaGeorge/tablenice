<?php

namespace Mystamyst\TableNice\Columns;

use Illuminate\Database\Eloquent\Model;

class IndexColumn extends Column
{
    protected int $startIndex = 1;
    protected int $currentPage = 1;
    protected int $perPage = 15;

    public static function make(string $name = 'index', ?string $label = '#'): static
    {
        return (new static($name, $label))->sticky();
    }

    public function paginated(int $currentPage, int $perPage): self
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        return $this;
    }

    public function toHtml(Model $model, int $loopIndex = 0): string
    {
        $rowIndex = (($this->currentPage - 1) * $this->perPage) + $loopIndex + $this->startIndex;

        $tooltipAttributes = '';
        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }

        $baseClasses = 'px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-slate-400';
        $finalClasses = trim(sprintf('%s %s %s', $baseClasses, $this->getAlignmentClass(), $this->getStickyClasses()));
        
        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        return sprintf(
            '<td class="%s" %s %s>%d</td>',
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            $rowIndex
        );
    }
}
