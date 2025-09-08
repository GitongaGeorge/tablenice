<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public bool $outlined;
    public string $size;
    public bool $disabled;
    public string $type;
    public string $tag;
    public ?string $href;
    public ?string $loadingText;
    public ?array $theme; // NEW: Theme property

    public function __construct(
        string $variant = 'primary',
        bool $outlined = false,
        string $size = 'md',
        bool $disabled = false,
        string $type = 'button',
        ?string $href = null,
        ?string $loadingText = 'Processing...',
        ?array $theme = null // NEW: Accept theme array
    ) {
        $this->variant = $variant;
        $this->outlined = $outlined;
        $this->size = $size;
        $this->disabled = $disabled;
        $this->type = $type;
        $this->href = $href;
        $this->tag = $href ? 'a' : 'button';
        $this->loadingText = $loadingText;
        $this->theme = $theme;
    }

    public function render()
    {
        return view('components.button');
    }
}
