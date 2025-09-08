<?php

namespace Mystamyst\TableNice\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class FormModal extends Component
{
    public bool $show = false;
    public string $title = '';
    public string $size;
    public ?string $component = null;
    public array $params = [];
    public array $theme = [];

    public function mount()
    {
        $this->size = config('tablenice.modal.default_width', '2xl');
    }

    #[On('showFormModal')]
    public function show($event)
    {
        $this->title = $event['title'] ?? '';
        $this->size = $event['size'] ?? config('tablenice.modal.default_width', '2xl');
        $this->component = $event['component'];
        $this->params = $event['params'] ?? [];
        $this->theme = $event['params']['theme'] ?? [];
        $this->show = true;
    }

    #[On('closeModal')]
    public function close()
    {
        $this->show = false;
        $this->component = null;
        $this->params = [];
        $this->title = '';
        $this->theme = [];
        $this->size = config('tablenice.modal.default_width', '2xl');
    }

    public function render()
    {
        return view('tablenice::livewire.components.form-modal');
    }
}

