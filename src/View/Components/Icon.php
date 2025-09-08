<?php

namespace Mystamyst\TableNice\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name The name of the icon to render (e.g., 'heroicon-s-cog-6-tooth').
     */
    public function __construct(public string $name)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // This correctly points to the Blade view where the attributes will be available.
        return view('tablenice::components.icon');
    }
}

