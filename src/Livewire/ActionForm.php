<?php

namespace Mystamyst\TableNice\Livewire;

use Mystamyst\TableNice\Actions\Action;
use Mystamyst\TableNice\Actions\PageAction;
use Mystamyst\TableNice\Forms\Components\FieldSet;
use Mystamyst\TableNice\Forms\Components\Section;
use Mystamyst\TableNice\Forms\Fields\Field;
use Mystamyst\TableNice\Forms\Fields\RichEditorField;
use Mystamyst\TableNice\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Mews\Purifier\Facades\Purifier;

class ActionForm extends Component
{
    public string $datatableClass;
    public string $actionName;
    public string|int|null $modelId = null;
    public array $form_data = [];
    public bool $saving = false;
    public bool $isViewOnly = false;
    public array $theme = [];

    public function mount(string $datatableClass, string $actionName, string|int|null $modelId = null, array $theme = [])
    {
        $this->datatableClass = $datatableClass;
        $this->actionName = $actionName;
        $this->modelId = $modelId;
        $this->theme = $theme;

        $action = $this->getAction();
        $form = $this->getForm();

        if (!$action || !$form) {
            // Handle case where action or form is not found
            return;
        }
        
        $this->isViewOnly = $action->isFormDisabled();
        $fields = $this->extractFields($form->getFields());

        if ($this->modelId) {
            // This is an Edit/View action
            $model = $this->getModel();
            if (!$model) {
                $this->dispatch('closeModal');
                $this->dispatch('showAlert', message: 'Record not found.', type: 'error', theme: $this->theme);
                return;
            }
            // Populate form data from the existing model
            foreach ($fields as $field) {
                $this->form_data[$field->getName()] = $model->{$field->getName()} ?? $field->getDefaultValue();
            }
        } else {
            // This is a Create action, populate with default values
            foreach ($fields as $field) {
                $this->form_data[$field->getName()] = $field->getDefaultValue();
            }
        }
    }

    private function extractFields(array $schema): array
    {
        $fields = [];
        foreach ($schema as $component) {
            if (!$component->isVisible()) continue;

            if ($component instanceof Field) {
                $fields[] = $component;
            } elseif ($component instanceof Section || $component instanceof FieldSet) {
                $fields = array_merge($fields, $this->extractFields($component->getSchema()));
            }
        }
        return $fields;
    }

    #[Computed]
    public function getAction(): ?Action
    {
        $datatable = app($this->datatableClass);
        $allActions = array_merge($datatable->actions(), $datatable->pageActions());
        return collect($allActions)->first(fn(Action $action) => $action->getName() === $this->actionName);
    }

    #[Computed]
    public function getModel(): ?Model
    {
        if (is_null($this->modelId)) {
            return null;
        }
        $datatable = app($this->datatableClass);
        $modelClass = $datatable->model;
        return $modelClass::find($this->modelId);
    }

    #[Computed]
    public function getForm(): ?Form
    {
        return $this->getAction()?->getForm();
    }

    public function save()
    {
        if ($this->isViewOnly) return;
        $this->saving = true;

        try {
            $action = $this->getAction();
            $form = $this->getForm();

            if (!$action || !$form) return;
            
            $fields = $this->extractFields($form->getFields());
            
            $rules = collect($fields)
                ->mapWithKeys(fn ($field) => ['form_data.' . $field->getName() => $field->getRules()])
                ->all();

            $messages = collect($fields)
                ->mapWithKeys(function ($field) {
                    $fieldMessages = [];
                    foreach ($field->getValidationMessages() as $rule => $message) {
                        $fieldMessages['form_data.' . $field->getName() . '.' . $rule] = $message;
                    }
                    return $fieldMessages;
                })->all();
                
            $this->validate($rules, $messages);

            $formData = $this->form_data;
            foreach ($fields as $field) {
                if ($field instanceof RichEditorField && isset($formData[$field->getName()])) {
                    $formData[$field->getName()] = Purifier::clean($formData[$field->getName()]);
                }
            }
            
            if ($action instanceof PageAction) {
                // Let the CreateAction handle model creation
                $action->run($formData);
            } else {
                // For EditAction, get the model and update it
                $model = $this->getModel();
                if ($model) {
                    $action->runOnModel($model, $formData);
                } else {
                     $this->dispatch('showAlert', message: 'Error: Could not find record to update.', type: 'error', theme: $this->theme);
                     return;
                }
            }

            $this->dispatch('closeModal');
            $this->dispatch('refreshDatatable');

            if ($message = $action->getSuccessMessage()) {
                $this->dispatch('showAlert', message: $message, type: 'success', theme: $this->theme);
            }
        } finally {
            $this->saving = false;
        }
    }

    public function render()
    {
        return view('tablenice::livewire.components.action-form');
    }
}
