<?php

namespace Mystamyst\TableNice\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ConfirmationModal extends Component
{
    public bool $show = false;
    public ?string $title = 'Are you sure?';
    public ?string $message = 'This action cannot be undone.';
    public ?string $confirmButtonText = 'Confirm';
    public ?string $cancelButtonText = 'Cancel';
    public ?string $method = null;
    public array $params = [];
    public array $theme = [];

    #[On('showConfirmation')]
    public function show($event)
    {
        $this->title = $event['title'] ?? $this->title;
        $this->message = $event['message'] ?? $this->message;
        $this->confirmButtonText = $event['confirmButtonText'] ?? $this->confirmButtonText;
        $this->cancelButtonText = $event['cancelButtonText'] ?? $this->cancelButtonText;
        $this->method = $event['method'];
        $this->params = $event['params'] ?? [];
        $this->theme = $event['theme'] ?? [];
        $this->show = true;
    }

    public function confirm()
    {
        if ($this->method) {
            $this->dispatch($this->method, ...$this->params);
        }
        $this->close();
    }

    public function close()
    {
        $this->show = false;
        $this->theme = [];
    }

    public function render()
    {
        return view('tablenice::livewire.components.confirmation-modal');
    }
}
