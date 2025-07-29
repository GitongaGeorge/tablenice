<?php

namespace Mystamyst\Tablenice\Forms\Components;

use Livewire\Component;

class Section extends Component
{
    public string $title;
    public ?string $description = null;
    public bool $collapsible = false;
    public bool $collapsed = false;

    public function mount(string $title, ?string $description = null, bool $collapsible = false, bool $collapsed = false)
    {
        $this->title = $title;
        $this->description = $description;
        $this->collapsible = $collapsible;
        $this->collapsed = $collapsed;
    }

    public function toggleCollapse()
    {
        if ($this->collapsible) {
            $this->collapsed = !$this->collapsed;
        }
    }

    public function render()
    {
        return \view('tablenice::forms.section');
    }
}