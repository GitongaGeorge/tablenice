# TableNice by mystamyst

TableNice is a powerful, themeable, and highly configurable datatable and form system for Laravel, built on the modern TALL stack (Tailwind CSS, Alpine.js, Livewire, Laravel). It provides a fluent, object-oriented, and developer-friendly API to create beautiful and interactive data-driven UIs with minimal effort.

## Features
- 🚀 **Fluent & Declarative API**: Define your tables and forms entirely in PHP using expressive, chainable methods.
- 🎨 **Powerful Theming Engine**: Choose from multiple pre-built color themes or create your own. A single line of code changes the entire look and feel.
- 🎛️ **Rich Column Types**: Includes Text, Number, DateTime, Image, Icon, and Relation columns out of the box, each with specialized formatters and options.
- ⚡ **Interactive Actions**: Create row, page, and bulk actions with built-in support for forms in modals and confirmation dialogs.
- 📈 **Data Visualization**: Display summary cards with dynamically generated charts (bar, line, pie, doughnut) to provide key insights at a glance.
- **Advanced Table Features**: Includes global searching, per-column filtering, sorting, grouping, summary rows, and customizable pagination.
- 🤖 **Code Generation**: Comes with `make:datatable` and `make:datatable-form` Artisan commands to instantly scaffold your tables and forms.
- 🛡️ **Type-Safe by Design**: Leverages modern PHP Enums for icons and colors, providing autocompletion and preventing common errors.
- 🧩 **Component-Based**: Built with a clean separation of concerns using Livewire and Blade components for maximum flexibility and reusability.
- 📱 **Fully Responsive**: Designed with a mobile-first approach to ensure a seamless experience on any device.

## Why TableNice?
- **Rapid Development**: Go from a model to a full-featured CRUD interface in minutes with the included Artisan generators.
- **High Customizability**: The object-oriented architecture allows you to extend and customize every aspect of the table, from column rendering to action logic.
- **Great Developer Experience (DX)**: The fluent API, combined with type-safety, makes building complex tables an intuitive and error-free process.
- **Excellent User Experience (UX)**: With reactive components, smooth transitions, and clear loading states, your users get a fast and polished interface.

## Installation
You can install the package via composer:

```bash
composer require mystamyst/tablenice
```

Next, publish the package's configuration file and views:

```bash
php artisan vendor:publish --provider="Mystamyst\TableNice\TableNiceServiceProvider"
```

This will publish:
- `tablenice.php` to your config directory.
- Views to `resources/views/vendor/tablenice`.
- Frontend assets (minimal, relies on your project's setup).

Finally, include the global Livewire components for modals and alerts in your main layout blade file (e.g., `resources/views/layouts/app.blade.php`), just before the closing `</body>` tag.

```blade
<body>
    ...
    
    <livewire:tablenice-alert />
    <livewire:tablenice-form-modal />
    <livewire:tablenice-confirmation-modal />
    
    @livewireScripts
</body>
```

## Basic Usage

### 1. Generate a Datatable
Use the provided Artisan command:

```bash
php artisan make:datatable UserTable --model=User
```

This creates:
- `app/DataTables/UserTable.php`
- `app/DataTables/Forms/UserTableForm.php`

### 2. Create a Route

```php
// routes/web.php
use App\DataTables\UserTable;
use Mystamyst\TableNice\Livewire\DatatableComponent;

// Full-page Livewire component
Route::get('/users', DatatableComponent::class)->with('tableClass', UserTable::class);

// Or inside a Blade view
Route::get('/dashboard/users', function () {
    return view('users.index');
});
```

Inside Blade:

```blade
{{-- resources/views/users/index.blade.php --}}
<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8">
        <livewire:tablenice-datatable :tableClass="\App\DataTables\UserTable::class" />
    </div>
</x-app-layout>
```

### 3. Customize Your Table

```php
<?php

namespace App\DataTables;

use Mystamyst\TableNice\Actions\CreateAction;
use Mystamyst\TableNice\Actions\DeleteAction;
use Mystamyst\TableNice\Actions\EditAction;
use Mystamyst\TableNice\Columns\DateTimeColumn;
use Mystamyst\TableNice\Columns\TextColumn;
use Mystamyst\TableNice\Enums\Theme;
use Mystamyst\TableNice\Table;
use App\Models\User;
use App\DataTables\Forms\UserTableForm;

class UserTable extends Table
{
    public string $model = User::class;

    public function columns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable(),

            TextColumn::make('email')
                ->searchable(),

            DateTimeColumn::make('created_at')
                ->label('Joined On')
                ->since()
                ->sortable(),
        ];
    }

    public function theme(): Theme
    {
        return Theme::INDIGO;
    }

    public function pageActions(): array
    {
        return [
            CreateAction::make()
                ->form(UserTableForm::class),
        ];
    }

    public function actions(): array
    {
        return [
            EditAction::make()
                ->form(UserTableForm::class),
            
            DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
}
```

---
✅ That's it! You now have a fully functional, beautiful, and interactive datatable for your users.
