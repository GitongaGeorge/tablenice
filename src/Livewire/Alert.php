<?php

namespace Mystamyst\TableNice\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class Alert extends Component
{
    public bool $show = false;
    public string $message = '';
    public string $type = 'success';
    public array $theme = []; // To hold theme classes
    public ?array $icon = null;

    /**
     * Listens for the 'showAlert' event to display the alert.
     */
    #[On('showAlert')]
    public function showAlert(string $message, string $type = 'success', ?array $icon = null, array $theme = [])
    {
        $this->message = $message;
        $this->type = $type;
        $this->icon = $icon;
        $this->theme = $theme; // Capture the theme
        $this->show = true;
    }

    /**
     * Computes the appropriate CSS classes for the alert based on its type and the provided theme.
     */
    #[Computed]
    public function styles(): array
    {
        // Default colors if no theme is provided
        $defaultColors = [
            'success' => ['bg' => 'bg-green-500'],
            'error'   => ['bg' => 'bg-red-500'],
            'warning' => ['bg' => 'bg-yellow-500'],
            'info'    => ['bg' => 'bg-blue-500'],
        ];

        $colorSet = $defaultColors[$this->type] ?? $defaultColors['info'];

        $containerBg = $this->theme['headerBgSolid'] ?? $colorSet['bg'];
        $iconColor = 'text-white';

        if ($this->type === 'error') {
            $containerBg = 'bg-gradient-to-br from-red-500 to-rose-600';
        }

        return [
            'container' => "{$containerBg} text-white",
            'icon'      => $iconColor,
        ];
    }

    public function render()
    {
        return view('tablenice::livewire.components.alert');
    }
}
