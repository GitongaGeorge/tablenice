<?php

namespace Mystamyst\Tablenice;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mystamyst\Tablenice\Console\Commands\MakeColumnCommand;
use Mystamyst\Tablenice\Console\Commands\MakeDatatableCommand;
use Mystamyst\Tablenice\Console\Commands\MakeFormCommand;
use Livewire\Livewire;
use Mystamyst\Tablenice\Forms\Components\Button;
use Mystamyst\Tablenice\Forms\Components\Modal;
use Mystamyst\Tablenice\Forms\Components\Section;
use Mystamyst\Tablenice\Forms\Components\Wizard;
use Mystamyst\Tablenice\Forms\Fields\CheckboxField;
use Mystamyst\Tablenice\Forms\Fields\DateField;
use Mystamyst\Tablenice\Forms\Fields\DateTimeField;
use Mystamyst\Tablenice\Forms\Fields\RadioField;
use Mystamyst\Tablenice\Forms\Fields\RelationshipSelectField;
use Mystamyst\Tablenice\Forms\Fields\SelectField;
use Mystamyst\Tablenice\Forms\Fields\TextInput;
use Mystamyst\Tablenice\Forms\Fields\TextareaField;
use function config_path;

class TableniceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tablenice.php', 'tablenice'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Views', 'tablenice');

        $this->publishes([
            __DIR__.'/../config/tablenice.php' => config_path('tablenice.php'),
        ], 'tablenice-config');

        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/tablenice'),
        ], 'tablenice-views');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeColumnCommand::class,
                MakeDatatableCommand::class,
                MakeFormCommand::class,
            ]);
        }

        $this->registerLivewireComponents();
        $this->registerBladeComponents();
    }

    protected function registerLivewireComponents()
    {
        // Forms Components
        Livewire::component('tablenice::forms.modal', Modal::class);
        Livewire::component('tablenice::forms.button', Button::class);
        Livewire::component('tablenice::forms.wizard', Wizard::class);
        Livewire::component('tablenice::forms.section', Section::class);

        // Datatable components (if any, though many will be pure Blade)
        // Livewire::component('tablenice::datatable', \Mystamyst\Tablenice\Core\Datatable::class); // Not usually directly registered as a root component
    }

    protected function registerBladeComponents()
    {
        // Datatable components
        Blade::component('tablenice::datatable.table', 'tablenice-table');
        Blade::component('tablenice::datatable.header', 'tablenice-table-header');
        Blade::component('tablenice::datatable.footer', 'tablenice-table-footer');
        Blade::component('tablenice::datatable.filters', 'tablenice-table-filters');
        Blade::component('tablenice::datatable.column-selector', 'tablenice-table-column-selector');
        Blade::component('tablenice::datatable.pagination', 'tablenice-table-pagination');
        Blade::component('tablenice::datatable.actions', 'tablenice-table-actions');
        Blade::component('tablenice::datatable.tabs', 'tablenice-table-tabs');


        // Form fields (as Blade components for easier rendering)
        Blade::component('tablenice::forms.text-input', 'tablenice-text-input');
        Blade::component('tablenice::forms.select-field', 'tablenice-select-field');
        Blade::component('tablenice::forms.checkbox-field', 'tablenice-checkbox-field');
        Blade::component('tablenice::forms.date-field', 'tablenice-date-field');
        Blade::component('tablenice::forms.datetime-field', 'tablenice-datetime-field');
        Blade::component('tablenice::forms.radio-field', 'tablenice-radio-field');
        Blade::component('tablenice::forms.textarea-field', 'tablenice-textarea-field');
    }
}