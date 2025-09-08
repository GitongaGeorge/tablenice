<?php

namespace Mystamyst\TableNice\Actions;

use Mystamyst\TableNice\Enums\Icons\HeroiconsIcon;

/**
 * A pre-built, reusable PageAction for creating new records.
 */
class CreateAction extends PageAction
{
    public function __construct(string $name = 'create')
    {
        parent::__construct($name);
        $this->label('Create');
        $this->icon(HeroiconsIcon::S_PLUS);
        $this->successMessage('Record created successfully.');
    }

    /**
     * Static factory method for cleaner instantiation.
     */
    public static function make(string $name = 'create'): static
    {
        return new static($name);
    }

    /**
     * The run method for CreateAction handles the creation of a new model instance.
     */
    public function run(array $data = [])
    {
        if (!$this->form) {
            return;
        }

        $formInstance = $this->getForm();
        if (property_exists($formInstance, 'model')) {
            $modelClass = $formInstance->model;
            if ($modelClass) {
                $modelClass::create($data);
            }
        }
    }
}
