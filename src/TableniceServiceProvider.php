<?php

namespace Mystamyst\Tablenice;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mystamyst\Tablenice\Console\Commands\MakeColumnCommand;
use Mystamyst\Tablenice\Console\Commands\MakeDatatableCommand;
use Mystamyst\Tablenice\Console\Commands\MakeFormCommand;
use Livewire\Livewire;
// Using aliases for Livewire components to avoid potential name conflicts
use Mystamyst\Tablenice\Forms\Components\Button as LivewireButtonComponent;
use Mystamyst\Tablenice\Forms\Components\Modal as LivewireModalComponent;
use Mystamyst\Tablenice\Forms\Components\Section as LivewireSectionComponent;
use Mystamyst\Tablenice\Forms\Components\Wizard as LivewireWizardComponent;
use Mystamyst\Tablenice\Forms\Fields\CheckboxField;
use Mystamyst\Tablenice\Forms\Fields\DateField;
use Mystamyst\Tablenice\Forms\Fields\DateTimeField;
use Mystamyst\Tablenice\Forms\Fields\RadioField;
use Mystamyst\Tablenice\Forms\Fields\RelationshipSelectField;
use Mystamyst\Tablenice\Forms\Fields\SelectField;
use Mystamyst\Tablenice\Forms\Fields\TextInput;
use Mystamyst\Tablenice\Forms\Fields\TextareaField;

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
        // This maps your package's src/Views directory to the 'tablenice' view namespace
        $this->loadViewsFrom(__DIR__.'/Views', 'tablenice');

        $this->publishes([
            __DIR__.'/../config/tablenice.php' => \config_path('tablenice.php'),
        ], 'tablenice-config');

        // This publishes your package's views to resources/views/vendor/tablenice
        $this->publishes([
            __DIR__.'/Views' => \resource_path('views/vendor/tablenice'),
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
        // Livewire components are registered using Livewire::component()
        Livewire::component('tablenice::forms.modal', LivewireModalComponent::class);
        Livewire::component('tablenice::forms.button', LivewireButtonComponent::class);
        Livewire::component('tablenice::forms.wizard', LivewireWizardComponent::class);
        Livewire::component('tablenice::forms.section', LivewireSectionComponent::class);

        // You might consider Livewire-specific registration for your main Datatable component
        // Livewire::component('tablenice-datatable', \Mystamyst\Tablenice\Core\Datatable::class);
    }

    protected function registerBladeComponents()
    {
        // --- THIS IS THE FIX ---
        // We use Blade::aliasComponent() to register a short alias (e.g., <x-tablenice-table>)
        // to a specific Blade view file (e.g., 'tablenice::components.datatable.table').

        // Datatable components
        \Blade::aliasComponent('tablenice::components.datatable.table', 'tablenice-table');
        \Blade::aliasComponent('tablenice::components.datatable.header', 'tablenice-table-header');
        \Blade::aliasComponent('tablenice::components.datatable.footer', 'tablenice-table-footer');
        \Blade::aliasComponent('tablenice::components.datatable.filters', 'tablenice-table-filters');
        \Blade::aliasComponent('tablenice::components.datatable.column-selector', 'tablenice-table-column-selector');
        \Blade::aliasComponent('tablenice::components.datatable.pagination', 'tablenice-table-pagination');
        \Blade::aliasComponent('tablenice::components.datatable.actions', 'tablenice-table-actions');
        \Blade::aliasComponent('tablenice::components.datatable.tabs', 'tablenice-table-tabs');


        // Form fields (as Blade components for easier rendering)
        // This will fix the 'tablenice-button' error
        \Blade::aliasComponent('tablenice::forms.text-input', 'tablenice-text-input');
        \Blade::aliasComponent('tablenice::forms.select-field', 'tablenice-select-field');
        \Blade::aliasComponent('tablenice::forms.checkbox-field', 'tablenice-checkbox-field');
        \Blade::aliasComponent('tablenice::forms.date-field', 'tablenice-date-field');
        \Blade::aliasComponent('tablenice::forms.datetime-field', 'tablenice-datetime-field');
        \Blade::aliasComponent('tablenice::forms.radio-field', 'tablenice-radio-field');
        \Blade::aliasComponent('tablenice::forms.textarea-field', 'tablenice-textarea-field');
        // --- END FIX ---
    }
}