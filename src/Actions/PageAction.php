<?php

namespace Mystamyst\TableNice\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents an action that is not tied to a specific row,
 * such as a "Create New Record" button. It extends the base Action
 * class to inherit features like forms, icons, and labels.
 */
abstract class PageAction extends Action
{
    /**
     * The primary execution method for a PageAction.
     * It does not receive a model instance.
     *
     * @param array $data Data from the form, if any.
     * @return void
     */
    abstract public function run(array $data = []);

    /**
     * Overrides the parent method from Action. This is not used by PageAction
     * and is implemented to satisfy the abstract contract.
     *
     * @param Model $model
     * @param array $data
     * @return void
     */
    final public function runOnModel(Model $model, array $data = [])
    {
        // This method is intentionally left empty as it is not applicable to PageActions.
        // The `run` method should be used instead.
    }
}
