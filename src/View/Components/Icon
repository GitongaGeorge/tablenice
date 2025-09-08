<?php

namespace Mystamyst\TableNice\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Blade;

class Icon extends Component
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function render()
    {
        // This renders the SVG icon directly using the blade-ui-kit's functionality.
        // It avoids conflicts with the host application's <x-icon> component.
        return Blade::render('<svg {{ $attributes }} >@svg("'.$this->name.'")</svg>');
    }
}
