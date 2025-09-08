<?php

namespace Mystamyst\TableNice\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeDatatableCommand extends GeneratorCommand
{
    protected $name = 'make:datatable';
    protected $description = 'Create a new TableNice datatable configuration class and its associated form';
    protected $type = 'Table';

    protected function getStub()
    {
        // Use the stub from within the package
        return __DIR__ . '/stubs/datatable.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\DataTables';
    }

    public function handle()
    {
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        if ($this->option('route')) {
            $this->addRoute();
        }
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $modelName = $this->option('model');
        $theme = Str::upper($this->option('theme'));

        if (!$modelName) {
            $this->error('A --model option is required.');
            return '';
        }

        $formName = $this->argument('name') . 'Form';
        $formClass = $this->qualifyClass($formName, 'Forms');
        $modelClass = $this->qualifyModel($modelName);

        $stub = str_replace(['{{ modelName }}', '{{modelName}}'], $modelName, $stub);
        $stub = str_replace(['{{ formClass }}', '{{formClass}}'], $formClass, $stub);
        $stub = str_replace(['{{ columns }}', '{{columns}}'], $this->generateColumns($modelClass), $stub);
        $stub = str_replace(['{{ theme }}', '{{theme}}'], $theme, $stub);

        $this->call('make:datatable-form', [
            'name' => $formName,
            '--model' => $modelName,
            '--force' => $this->option('force'),
        ]);

        return $stub;
    }

    /**
     * Generate the columns for the datatable.
     * APPLIES USER-REQUESTED FORMATTING.
     */
    protected function generateColumns($modelClass)
    {
        if (!class_exists($modelClass)) {
            return '// Model not found. Please add columns manually.';
        }

        $model = new $modelClass;
        $fillable = $model->getFillable();
        $columns = '';

        $exclude = ['password', 'remember_token', 'email_verified_at'];

        foreach ($fillable as $field) {
            if (in_array($field, $exclude)) {
                continue;
            }
            $columns .= "\t\t\tTextColumn::make('{$field}')\n";
            $columns .= "\t\t\t\t->sortable()\n";
            $columns .= "\t\t\t\t->searchable(),\n\n";
        }
        
        $columns .= "\t\t\tDateTimeColumn::make('created_at')\n";
        $columns .= "\t\t\t\t->sortable(),\n";

        return rtrim($columns, "\n");
    }

    protected function qualifyClass($name, $subNamespace = null)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $namespace = is_null($subNamespace)
            ? $this->getDefaultNamespace(trim($rootNamespace, '\\'))
            : trim($rootNamespace, '\\') . '\\DataTables\\' . $subNamespace;

        return $namespace . '\\' . $name;
    }

    protected function addRoute()
    {
        $tableClass = $this->qualifyClass($this->getNameInput());
        $routeName = Str::of($this->getNameInput())->replace('Table', '')->kebab();
        $routeNameToSearch = "'" . $routeName . ".index'";
        
        $routesPath = base_path('routes/web.php');
        $routesContent = File::get($routesPath);

        if (Str::contains($routesContent, $routeNameToSearch)) {
            $this->info("Route [{$routeName}.index] already exists. Skipping.");
            return;
        }

        $livewireComponentRoute = "\nRoute::get('/" . $routeName . "', \App\Http\Livewire\DatatablePage::class)"
            . "\n    ->with('tableClass', \\" . $tableClass . "::class)"
            . "\n    ->middleware('auth')->name({$routeNameToSearch});";

        File::append($routesPath, $livewireComponentRoute);
        $this->info("Route [{$routeName}.index] added to routes/web.php");
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the datatable class (e.g., UserTable)'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model class for the datatable.'],
            ['route', 'r', InputOption::VALUE_NONE, 'Generate a route for this datatable.'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the table already exists'],
            ['theme', null, InputOption::VALUE_OPTIONAL, 'The color theme for the datatable (blue, indigo, emerald, rose, teal, orange, slate)', 'blue'],
        ];
    }
}
