<?php

namespace Mystamyst\Tablenice\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str; // Added for Str::singular and class_basename

class MakeFormCommand extends GeneratorCommand
{
    protected $name = 'make:tablenice-form';
    protected $description = 'Create a new Tablenice form class';
    protected $type = 'Form';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/form.stub';
    }

    // --- UPDATED: Default namespace for Forms ---
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Forms'; // A general namespace for form definitions
    }

    protected function replaceClass($stub, $name): string
    {
        $stub = parent::replaceClass($stub, $name);
        // Assuming the model name is derived from the form name, e.g., UserForm -> User
        $modelName = Str::singular(str_replace('Form', '', class_basename($this->argument('name'))));
        return str_replace('DummyModel', $modelName, $stub);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form class.'],
        ];
    }
}