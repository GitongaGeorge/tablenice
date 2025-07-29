<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Mystamyst\Tablenice\Forms\Form;
use Illuminate\Support\Facades\Session;

trait WithForms
{
    public bool $showFormModal = false;
    public ?string $modalTitle = null;
    public ?string $modalFormClass = null; // The FQCN of the form to display
    public array $modalFormFields = [];
    public ?object $modalRecord = null;
    public array $formData = [];

    /**
     * Open a form modal.
     *
     * @param string $formClass The fully qualified class name of the Form
     * @param mixed|null $record The model instance to edit, if any
     */
    public function openFormModal(string $formClass, $recordId = null)
    {
        if (!class_exists($formClass)) {
            Session::flash('error', 'Invalid form class provided.'); 
            return;
        }

        /** @var Form $formInstance */
        $formInstance = \app($formClass);
        $this->modalFormClass = $formClass;
        $this->modalTitle = $formInstance->getTitle();
        $this->modalFormFields = $formInstance->getFormFields();
        $this->modalRecord = null;
        $this->formData = [];

        if ($recordId) {
            $model = $this->query()->find($recordId); // Use the datatable's query to find the model
            if ($model) {
                $this->modalRecord = $model;
                $this->formData = $model->toArray(); // Populate form with existing data
            }
        }

        $this->showFormModal = true;
    }

    /**
     * Close the form modal.
     */
    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->reset(['modalTitle', 'modalFormClass', 'modalFormFields', 'modalRecord', 'formData']);
    }

    /**
     * Save/submit the form data.
     */
    public function saveForm()
    {
        if (!$this->modalFormClass) {
            return;
        }

        /** @var Form $formInstance */
        $formInstance = \app($this->modalFormClass);

        $rules = $formInstance->getValidationRules($this->formData, $this->modalRecord);
        $this->validate($rules);

        try {
            if ($this->modalRecord) {
                Session::flash('success', 'Record updated successfully!');
            } else {
                $formInstance->create($this->formData);
                Session::flash('success', 'Record created successfully!');
            }
            $this->closeFormModal();
            $this->dispatch('refreshDatatable'); // Notify datatable to refresh
        } catch (\Exception $e) {
            Session::flash('error', 'Error saving record: ' . $e->getMessage());
        }
    }
}