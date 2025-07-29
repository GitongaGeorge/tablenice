<?php

namespace Mystamyst\Tablenice\Columns;

class ImageColumn extends Column
{
    protected ?string $defaultImageUrl = null;
    protected ?string $disk = null; // For Spatie Media Library or similar
    protected ?int $width = 40;
    protected ?int $height = 40;
    protected string $alt = '';
    protected string $shape = 'square'; // square, circle, rounded

    public function defaultImageUrl(string $url): static
    {
        $this->defaultImageUrl = $url;
        return $this;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;
        return $this;
    }

    public function size(int $width, ?int $height = null): static
    {
        $this->width = $width;
        $this->height = $height ?? $width;
        return $this;
    }

    public function alt(string $alt): static
    {
        $this->alt = $alt;
        return $this;
    }

    public function shape(string $shape): static
    {
        $this->shape = $shape;
        return $this;
    }

    public function render(\Illuminate\Database\Eloquent\Model $record)
    {
        $imageUrl = parent::getValue($record) ?? $this->defaultImageUrl;

        if (!$imageUrl && $this->disk) {
            // Logic for Spatie Media Library if needed:
            // $media = $record->getFirstMedia($this->attribute);
            // $imageUrl = $media ? $media->getUrl() : null;
        }

        $style = "width: {$this->width}px; height: {$this->height}px;";
        $classes = 'object-cover';
        if ($this->shape === 'circle') {
            $classes .= ' rounded-full';
        } elseif ($this->shape === 'rounded') {
            $classes .= ' rounded-md';
        }

        return $imageUrl ? "<img src=\"{$imageUrl}\" alt=\"{$this->alt}\" class=\"{$classes}\" style=\"{$style}\" />" : '';
    }
}