<?php

namespace Mystamyst\TableNice\Forms\Fields;

use Illuminate\Database\Eloquent\Model;

class TextInput extends Field
{
    protected string $type = 'text';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function toHtml(): string
    {
        // This method is not used by the main action-form, which handles rendering directly.
        // It's kept for potential standalone use.
        return sprintf(
            '<div class="mb-4">
                <label for="%s" class="block text-sm font-medium text-gray-700 dark:text-gray-300">%s</label>
                <input type="%s" name="%s" id="%s" value="%s" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">
            </div>',
            $this->getName(),
            $this->getLabel(),
            $this->getType(),
            $this->getName(),
            $this->getName(),
            $this->getDefaultValue() ?? ''
        );
    }
}
