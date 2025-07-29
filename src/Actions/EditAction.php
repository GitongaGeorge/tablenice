<?php

namespace Mystamyst\Tablenice\Actions;

use Mystamyst\Tablenice\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class EditAction extends Action
{
    protected ?string $formClass = null;

    public function __construct(string $name = 'edit', string $label = 'Edit')
    {
        parent::__construct($name, $label);
        $this->icon('heroicon-o-pencil');
        $this->color('primary');
        $this->confirmation = 'Are you sure you want to edit this record?';
    }

    public function form(string $formClass): static
    {
        $this->formClass = $formClass;
        return $this;
    }

    public function getFormClass(): ?string
    {
        return $this->formClass;
    }

    public function handle(...$records): mixed
    {
        if (empty($records) || !($records[0] instanceof Model)) {
            Session::flash('error', 'No record provided for editing.');
            return null;
        }

        $record = $records[0];

        if ($this->before instanceof \Closure) {
            ($this->before)($record);
        }

        if ($this->formClass) {
            // Emit Livewire event to open the form modal
            $this->dispatch('openFormModal', $this->formClass, $record->getKey());
        } else {
            Session::flash('error', 'No form defined for this edit action.');
        }

        if ($this->after instanceof \Closure) {
            ($this->after)($record);
        }

        return null;
    }
}