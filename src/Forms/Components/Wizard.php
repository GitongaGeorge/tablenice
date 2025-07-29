<?php

namespace Mystamyst\Tablenice\Forms\Components;

use Livewire\Component;

class Wizard extends Component
{
    public int $currentStep = 1;
    public array $steps = []; // Array of step titles or components
    public array $formData = [];

    protected $listeners = ['goToStep', 'nextStep', 'prevStep']; // Example listeners

    public function mount(array $steps, array $formData = [])
    {
        $this->steps = $steps;
        $this->formData = $formData;
    }

    public function nextStep()
    {
        if ($this->currentStep < count($this->steps)) {
            // Optional: Validate current step's data before proceeding
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= count($this->steps)) {
            $this->currentStep = $step;
        }
    }

    public function render()
    {
        return \view('tablenice::forms.wizard');
    }
}