<?php

namespace Mystamyst\TableNice\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeDatatableFormCommand extends GeneratorCommand
{
    protected $name = 'make:datatable-form';
    protected $description = 'Create a new TableNice form class';
    protected $type = 'Form';

    protected function getStub()
    {
        // Use the stub from within the package
        return __DIR__ . '/stubs/datatable.form.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\DataTables\Forms';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $modelName = $this->option('model');

        if (!$modelName) {
            return $stub;
        }
        
        $modelClass = $this->qualifyModel($modelName);

        $stub = str_replace(['{{ modelClass }}', '{{modelClass}}'], $modelClass, $stub);
        $stub = str_replace(['{{ modelName }}', '{{modelName}}'], $modelName, $stub);
        $stub = str_replace(['{{ fields }}', '{{fields}}'], $this->generateFields($modelClass), $stub);

        return $stub;
    }

    /**
     * Generate the fields for the form.
     * APPLIES USER-REQUESTED FORMATTING.
     */
    protected function generateFields($modelClass)
    {
        if (!class_exists($modelClass)) {
            return '// Model not found. Please add fields manually.';
        }

        $model = new $modelClass;
        $fillable = $model->getFillable();
        $fields = '';

        $exclude = [
            'password', 
            'remember_token', 
            'email_verified_at',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        foreach ($fillable as $field) {
            if (in_array($field, $exclude)) {
                continue;
            }
            $fields .= "\t\t\tTextInput::make('{$field}')\n";
            $fields .= "\t\t\t\t->required(),\n\n";
        }

        return rtrim($fields, "\n");
    }
    
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form class'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model for which the form is generated'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the form already exists'],
        ];
    }
}
