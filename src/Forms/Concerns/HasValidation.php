<?php

namespace Mystamyst\Tablenice\Forms\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

trait HasValidation
{
    /**
     * Get the validation rules for the form.
     *
     * @param array $data The current form data.
     * @param Model|null $record The model instance being updated, if any.
     * @return array
     */
    public function getValidationRules(array $data, ?Model $record = null): array
    {
        $rules = [];
        foreach ($this->getFormFields() as $field) {
            $fieldRules = $field->getRules();
            if ($fieldRules) {
                // Adjust unique rules for updates
                if ($record) {
                    $fieldRules = array_map(function ($rule) use ($record, $field) {
                        if (str_starts_with($rule, 'unique:')) {
                            // Example: unique:table,column,except,idColumn
                            $parts = explode(',', $rule);
                            $table = $parts[0] . ':' . $parts[1];
                            $column = $parts[2] ?? $field->getName(); // Use field name as column by default
                            return "unique:{$table},{$column},{$record->getKey()},{$record->getKeyName()}";
                        }
                        return $rule;
                    }, is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules);
                }
                $rules[$field->getName()] = $fieldRules;
            }
        }
        return $rules;
    }
}