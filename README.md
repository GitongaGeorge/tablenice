# TableNice by mystamyst

TableNice is a powerful, themeable, and highly configurable datatable and form system for Laravel, built on the modern TALL stack (Tailwind CSS, Alpine.js, Livewire, Laravel). It provides a fluent, object-oriented, and developer-friendly API to create beautiful and interactive data-driven UIs with minimal effort.

## Features

🚀 **Fluent & Declarative API**: Define your tables and forms entirely in PHP using expressive, chainable methods.

🎨 **Powerful Theming Engine**: Choose from multiple pre-built color themes or create your own. A single line of code changes the entire look and feel.

🎛️ **Rich Column Types**: Includes Text, Number, DateTime, Image, Icon, and Relation columns out of the box, each with specialized formatters and options.

⚡ **Interactive Actions**: Create row, page, and bulk actions with built-in support for forms in modals and confirmation dialogs.

📈 **Data Visualization**: Display summary cards with dynamically generated charts (bar, line, pie, doughnut) to provide key insights at a glance.

**Advanced Table Features**: Includes global searching, per-column filtering, sorting, grouping, summary rows, and customizable pagination.

🤖 **Code Generation**: Comes with a `make:datatable` command to instantly scaffold your tables and a `tablenice:install` command to get set up quickly.

🛡️ **Type-Safe by Design**: Leverages modern PHP Enums for icons and colors, providing autocompletion and preventing common errors.

🧩 **Component-Based**: Built with a clean separation of concerns using Livewire and Blade components for maximum flexibility and reusability.

📱 **Fully Responsive**: Designed with a mobile-first approach to ensure a seamless experience on any device.

## Installation & Setup

### 1. Require the Package

First, install the package via composer:

```bash
composer require mystamyst/tablenice
```

### 2. Run the Install Command

TableNice comes with a handy installation command that publishes the necessary assets and provides you with the next steps.

```bash
php artisan tablenice:install
```

This command will:

- Publish the `tablenice.php` configuration file to your `config/` directory.
- Publish the Blade views to `resources/views/vendor/tablenice/`, allowing you to customize them if needed.
- Provide you with clear, copy-pasteable instructions for the final manual steps.

### 3. Frontend Dependencies

The interactive elements of TableNice, like charts, calendars, and the rich text editor, require a few frontend packages. Install them via npm:

```bash
npm install chart.js dayjs litepicker trix
```

Then, import them in your main JavaScript file (e.g., `resources/js/app.js`):

```javascript
// resources/js/app.js
import Chart from 'chart.js/auto';
import 'litepicker/dist/plugins/mobilefriendly';
import Litepicker from 'litepicker';
import dayjs from 'dayjs';
import 'trix';

// Make libraries globally available for Alpine.js components
window.Chart = Chart;
window.Litepicker = Litepicker;
window.dayjs = dayjs;
```

### 4. Update Your Main Layout

For modals and alerts to work correctly across your entire application, you must add the global TableNice Livewire components to your main layout file (e.g., `resources/views/layouts/app.blade.php`). Place these tags just before the closing `</body>` tag.

```blade
    ...
    {{-- This allows TableNice to display alerts and modals anywhere in your app --}}
    <livewire:tablenice-alert />
    <livewire:tablenice-form-modal />
    <livewire:tablenice-confirmation-modal />
    
    @livewireScripts
</body>
```

## Usage

### 1. Generate a Datatable

Use the `make:datatable` command to generate a Table and its corresponding Form class.

```bash
php artisan make:datatable UserTable --model=User
```

#### Available Command Options

- `--model=User`: (Required) The Eloquent model to use for the table. The command will inspect this model to pre-fill columns and form fields.
- `--theme=indigo`: Specifies a color theme for the generated table class. Defaults to blue.
- `--route`: Automatically adds a full-page route for this datatable in `routes/web.php`.
- `--force`: Overwrites existing Table and Form files if they already exist.

### 2. Create a Route

If you didn't use the `--route` option during creation, you can add a route manually in `routes/web.php`:

```php
// routes/web.php
use App\DataTables\UserTable;
use Mystamyst\TableNice\Livewire\DatatableComponent;

// Create a full-page Livewire component route
Route::get('/users', DatatableComponent::class)->with('tableClass', UserTable::class);
```

### 3. Customize Your Table

Open `app/DataTables/UserTable.php` to configure your columns, actions, and theme. The generator provides a great starting point.

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
                ->since() // e.g., "2 days ago"
                ->sortable(),
        ];
    }
    
    public function theme(): Theme
    {
        return Theme::INDIGO; // Change the theme here
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

That's it! Visit your `/users` route to see your new datatable.