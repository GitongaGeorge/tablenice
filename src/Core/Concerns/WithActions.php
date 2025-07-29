<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Illuminate\Support\Facades\Session;
use Mystamyst\Tablenice\Actions\Action;

trait WithActions
{
    /**
     * An array of Action instances.
     *
     * @return array<Action>
     */
    public function getActions(): array
    {
        return [];
    }

    /**
     * Handle an action triggered from the datatable.
     *
     * @param string $actionName
     * @param mixed ...$params
     */
    public function callAction(string $actionName, ...$params)
    {
        $action = collect($this->getActions())->first(fn($a) => $a->getName() === $actionName);

        if ($action && $action->canRun()) {
            // Check if it's a bulk action
            if (method_exists($action, 'getBulkActionKey') && !empty($this->selectedRows)) {
                $action->handleBulk(collect($this->selectedRows));
                $this->selectedRows = []; // Clear selected rows after bulk action
            } else {
                $action->handle(...$params);
            }
        } else {
            // Optionally, dispatch a notification or log an error
            Session::flash('error', 'Action not found or unauthorized.');
        }
    }
}