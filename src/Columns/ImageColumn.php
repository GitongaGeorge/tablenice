<?php

namespace Mystamyst\TableNice\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ImageColumn extends Column
{
    protected bool $isCircular = false;
    protected bool $isStacked = false;
    protected int $stackedLimit = 3;
    protected string $size = 'h-10 w-10';
    protected bool $hasPopover = false;
    protected ?array $expandableConfig = null;

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label ?? '', 'image');
    }

    public function circular(bool $isCircular = true): self
    {
        $this->isCircular = $isCircular;
        return $this;
    }

    public function stacked(bool $isStacked = true, int $limit = 3): self
    {
        $this->isStacked = $isStacked;
        $this->stackedLimit = $limit;
        return $this;
    }

    public function size(string $sizeClass): self
    {
        $this->size = $sizeClass;
        return $this;
    }

    public function popoverOnHover(bool $hasPopover = true): self
    {
        $this->hasPopover = $hasPopover;
        $this->expandableConfig = null; // Ensure expandable is disabled
        return $this;
    }
    
    public function expandable(string $displayAttribute = 'name', string $imageAttribute = 'avatar_url'): self
    {
        $this->expandableConfig = [
            'display' => $displayAttribute,
            'image' => $imageAttribute,
        ];
        $this->hasPopover = false; // Ensure popover is disabled
        return $this;
    }

    public function format(callable $callback): self
    {
        $this->formatCallback = $callback;
        return $this;
    }

    public function toHtml(Model $model): string
    {
        $rawValue = $this->resolveValue($model);
        $data = $this->formatCallback ? call_user_func($this->formatCallback, $rawValue, $model) : $rawValue;

        $items = collect(is_array($data) || $data instanceof Collection ? $data : Arr::wrap($data))->filter();

        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        if ($items->isEmpty()) {
            $finalClasses = trim(sprintf('px-6 py-2 whitespace-nowrap %s %s', $this->getAlignmentClass(), $this->getStickyClasses()));
            return sprintf('<td class="%s" %s></td>', $finalClasses, $styleAttribute);
        }

        $contentHtml = ($items->count() === 1 && !$this->isStacked && !$this->expandableConfig)
            ? $this->renderSingleImage($items->first())
            : $this->renderStackedContent($items);

        $tooltipAttributes = '';
        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }

        $baseClasses = 'px-6 py-2 whitespace-nowrap align-top';
        $finalClasses = trim(sprintf('%s %s %s', $baseClasses, $this->getAlignmentClass(), $this->getStickyClasses()));
        
        $isExpandable = $this->expandableConfig && $items->count() > 1;
        $isPopover = $this->hasPopover && $items->count() > 1;
        $isInteractive = $isExpandable || $isPopover;

        $alpineJs = '';
        $dataAttribute = '';

        if ($isInteractive) {
            $interactiveMode = $isExpandable ? 'expand' : 'popover';
            $dataAttribute = 'data-interactive-mode="' . $interactiveMode . '"';

            $jsLogic = <<<'JS'
$watch('open', value => {
    if (value) {
        $nextTick(() => {
            const content = $el.querySelector('[x-ref="expandedContent"]');
            if (content) {
                const mode = $el.dataset.interactiveMode;
                // Use a smaller buffer for the list, and a larger one for the popover
                const buffer = mode === 'expand' ? 8 : 16;
                $el.style.paddingBottom = `${content.offsetHeight + buffer}px`;
            }
        });
    } else {
        $el.style.paddingBottom = '0.5rem';
    }
})
JS;
            $alpineJs = sprintf('x-init="%s"', htmlspecialchars($jsLogic));
        }
        
        $alpineWrapperEvents = $isInteractive ? '@mouseenter="open = true" @mouseleave="open = false"' : '';

        return sprintf(
            '<td x-data="{ open: false }" %s %s class="%s relative transition-all duration-200" %s %s>
                <div %s>%s</div>
            </td>',
            $dataAttribute,
            $alpineJs,
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            $alpineWrapperEvents,
            $contentHtml
        );
    }

    private function renderSingleImage($item): string
    {
        $imageUrl = is_string($item) ? $item : data_get($item, $this->expandableConfig['image'] ?? 'avatar_url');
        $shapeClass = $this->isCircular ? 'rounded-full' : 'rounded-md';
        return sprintf(
            '<img src="%s" alt="" class="%s %s object-cover">',
            e($imageUrl),
            $this->size,
            $shapeClass
        );
    }

    private function renderStackedContent(Collection $items): string
    {
        if ($this->expandableConfig) {
            return $this->renderExpandableList($items);
        }
        
        $popoverHtml = $this->hasPopover ? $this->renderPopover($items) : '';
        
        $imageUrls = $items->map(fn($item) => is_string($item) ? $item : data_get($item, 'avatar_url'));
        $imagesToShow = $imageUrls->slice(0, $this->stackedLimit);
        $remainingCount = $imageUrls->count() - $imagesToShow->count();
        $ringClasses = $this->theme['imageRing'] ?? 'ring-white dark:ring-slate-800';
        
        $imageElements = $imagesToShow->map(fn($url) => sprintf(
            '<img class="%s rounded-full object-cover ring-2 %s" src="%s" alt="">',
            $this->size, $ringClasses, e($url)
        ))->implode('');

        if ($remainingCount > 0) {
            $imageElements .= sprintf(
                '<div class="%s flex items-center justify-center rounded-full bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 ring-2 %s text-xs font-medium"><span>+%d</span></div>',
                $this->size, $ringClasses, $remainingCount
            );
        }

        $stackedHtml = sprintf('<div class="flex items-center -space-x-4">%s</div>', $imageElements);
        $wrapperClass = $this->hasPopover ? 'relative flex items-center' : '';

        return sprintf('<div class="%s">%s %s</div>', $wrapperClass, $stackedHtml, $popoverHtml);
    }

    private function renderExpandableList(Collection $items): string
    {
        $imageAttr = $this->expandableConfig['image'];
        $displayAttr = $this->expandableConfig['display'];
        $ringClasses = $this->theme['imageRing'] ?? 'ring-white dark:ring-slate-800';
        
        $collapsedImages = $items->slice(0, $this->stackedLimit);
        $collapsedElements = $collapsedImages->map(fn($item) => sprintf(
            '<img class="%s rounded-full object-cover ring-2 %s" src="%s" alt="">',
            $this->size, $ringClasses, e(data_get($item, $imageAttr))
        ))->implode('');

        $remainingCount = $items->count() - $collapsedImages->count();
        if ($remainingCount > 0) {
            $collapsedElements .= sprintf(
                '<div class="%s flex items-center justify-center rounded-full bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 ring-2 %s text-xs font-medium"><span>+%d</span></div>',
                $this->size, $ringClasses, $remainingCount
            );
        }
        $collapsedHtml = sprintf('<div class="flex items-center -space-x-4">%s</div>', $collapsedElements);

        $expandedListItems = $items->map(fn($item) => sprintf(
            '<div class="flex items-center gap-3">
                <img class="h-8 w-8 rounded-full object-cover" src="%s" alt="">
                <span class="text-sm text-gray-700 dark:text-gray-300">%s</span>
            </div>',
            e(data_get($item, $imageAttr)),
            e(data_get($item, $displayAttr))
        ))->implode('');
        
        $expandedHtml = sprintf(
            '<div x-ref="expandedContent" x-show="open" x-cloak x-transition class="mt-4 flex flex-col space-y-2">%s</div>',
            $expandedListItems
        );
        
        return '<div>' . $collapsedHtml . $expandedHtml . '</div>';
    }

    private function renderPopover(Collection $items): string
    {
        $imageUrls = $items->map(fn($item) => is_string($item) ? $item : data_get($item, 'avatar_url'));
        $popoverImages = $imageUrls->slice(0, 12);
        $remainingInPopover = $imageUrls->count() - $popoverImages->count();
            
        $popoverGridItems = $popoverImages->map(fn($url) => sprintf(
            '<div><img class="h-12 w-12 object-cover rounded-md" src="%s" alt=""></div>', e($url)
        ))->implode('');

        if ($remainingInPopover > 0) {
            $popoverGridItems .= sprintf(
                '<div class="flex items-center justify-center h-12 w-12 bg-slate-100 dark:bg-slate-700 rounded-md text-slate-500 dark:text-slate-400 font-bold">+%d</div>',
                $remainingInPopover
            );
        }

        $expandedHtml = sprintf(
            '<div x-ref="expandedContent" x-show="open" x-cloak x-transition class="absolute z-50 top-full left-0 mt-2 w-max max-w-sm p-2 bg-white dark:bg-slate-800 rounded-lg shadow-2xl ring-1 ring-black ring-opacity-5">
                <div class="grid grid-cols-4 gap-2">%s</div>
            </div>',
            $popoverGridItems
        );
        
        return $expandedHtml;
    }
}

