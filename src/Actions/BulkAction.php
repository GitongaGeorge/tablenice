<?php

namespace Mystamyst\TableNice\Actions;

use Illuminate\Database\Eloquent\Model;

class BulkAction extends Action
{
    /**
     * Static factory method for cleaner instantiation.
     * Note: The name is required here as bulk actions can be varied (e.g., 'delete', 'export').
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Defines the logic to be executed for each model in the bulk action.
     */
    public function runOnModel(Model $model, array $data = [])
    {
        // Example implementation: Delete the model.
        if ($this->getName() === 'delete') {
            $model->delete();
        }
    }
}
