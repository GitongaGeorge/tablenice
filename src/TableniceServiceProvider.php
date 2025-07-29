<?php

namespace Mystamyst\Tablenice;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mystamyst\Tablenice\Console\Commands\MakeColumnCommand;
use Mystamyst\Tablenice\Console\Commands\MakeDatatableCommand;
use Mystamyst\Tablenice\Console\Commands\MakeFormCommand;
use Livewire\Livewire;
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
        $this->loadViewsFrom(__DIR__.'/Views', 'tablenice');

        $this->publishes([
            __DIR__.'/../config/tablenice.php' => \config_path('tablenice.php'),
        ], 'tablenice-config');

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
        Livewire::component('tablenice::forms.modal', LivewireModalComponent::class);
        Livewire::component('tablenice::forms.button', LivewireButtonComponent::class);
        Livewire::component('tablenice::forms.wizard', LivewireWizardComponent::class);
        Livewire::component('tablenice::forms.section', LivewireSectionComponent::class);
    }

    protected function registerBladeComponents()
    {
        // --- FIXED: Changed aliases from kebab-case to snake_case ---

        // Datatable components
        \Blade::aliasComponent('tablenice::components.datatable.table', 'tablenice_table');
        \Blade::aliasComponent('tablenice::components.datatable.header', 'tablenice_table_header');
        \Blade::aliasComponent('tablenice::components.datatable.footer', 'tablenice_table_footer');
        \Blade::aliasComponent('tablenice::components.datatable.filters', 'tablenice_table_filters');
        \Blade::aliasComponent('tablenice::components.datatable.column-selector', 'tablenice_table_column_selector');
        \Blade::aliasComponent('tablenice::components.datatable.pagination', 'tablenice_table_pagination');
        \Blade::aliasComponent('tablenice::components.datatable.actions', 'tablenice_table_actions');
        \Blade::aliasComponent('tablenice::components.datatable.tabs', 'tablenice_table_tabs');


        // Form fields (as Blade components for easier rendering)
        \Blade::aliasComponent('tablenice::forms.text-input', 'tablenice_text_input');
        \Blade::aliasComponent('tablenice::forms.select-field', 'tablenice_select_field');
        \Blade::aliasComponent('tablenice::forms.checkbox-field', 'tablenice_checkbox_field');
        \Blade::aliasComponent('tablenice::forms.date-field', 'tablenice_date_field');
        \Blade::aliasComponent('tablenice::forms.datetime-field', 'tablenice_datetime_field');
        \Blade::aliasComponent('tablenice::forms.radio-field', 'tablenice_radio_field');
        \Blade::aliasComponent('tablenice::forms.textarea-field', 'tablenice_textarea_field');
    }
}