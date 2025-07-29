<?php

namespace Mystamyst\Tablenice\Forms\Components;

use Livewire\Component;

class Modal extends Component
{
    public bool $show = false;
    public string $title = '';
    public string $width = '2xl'; // Tailwind CSS width classes
    public bool $closeButton = true;

    protected $listeners = ['openModal', 'closeModal'];

    public function openModal(string $title = '', ?string $width = null, ?bool $closeButton = null)
    {
        $this->title = $title;
        $this->width = $width ?? config('tablenice.modal.default_width', '2xl');
        $this->closeButton = $closeButton ?? \config('tablenice.modal.default_close_button', true);
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
        $this->reset(['title', 'width', 'closeButton']);
    }

    public function render()
    {
        return \view('tablenice::forms.modal');
    }
}