<?php

namespace Mystamyst\Tablenice\Forms\Components;

use Livewire\Component;

class Button extends Component
{
    public string $label;
    public string $wireClick;
    public string $color = 'primary'; // Tailwind color classes, e.g., 'blue', 'red', 'green'
    public string $type = 'button'; // 'button', 'submit'
    public string $icon = '';
    public bool $loading = false; // Internal state for spinner
    public bool $disabled = false;
    public string $loadingTarget = ''; // The Livewire method to watch for loading state

    public function mount(string $label, string $wireClick, string $color = 'primary', string $type = 'button', string $icon = '', bool $disabled = false, string $loadingTarget = '')
    {
        $this->label = $label;
        $this->wireClick = $wireClick;
        $this->color = $color;
        $this->type = $type;
        $this->icon = $icon;
        $this->disabled = $disabled;
        $this->loadingTarget = $loadingTarget;
    }

    public function render()
    {
        return \view('tablenice::forms.button');
    }
}