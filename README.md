# TableNice by mystamyst

TableNice is a powerful, themeable, and highly configurable datatable and form system for Laravel, built on the modern TALL stack (Tailwind CSS, Alpine.js, Livewire, Laravel). It provides a fluent, object-oriented, and developer-friendly API to create beautiful and interactive data-driven UIs with minimal effort.

## Features

- 🚀 **Fluent & Declarative API**: Define your tables and forms entirely in PHP using expressive, chainable methods.
- 🎨 **Powerful Theming Engine**: Choose from multiple pre-built color themes or create your own. A single line of code changes the entire look and feel.
- **DataSource Agnostic**: Works seamlessly with Eloquent models, custom queries, and even API-driven collections.
- 🎛️ **Rich Column Types**: Includes Text, Number, DateTime, Image, Icon, and Relation columns out of the box, each with specialized formatters and options.
- ⚡ **Interactive Actions**: Create row, page, and bulk actions with built-in support for forms in modals and confirmation dialogs.
- 📈 **Data Visualization**: Display summary cards with dynamically generated charts (bar, line, pie, doughnut) to provide key insights at a glance.
- **Advanced Table Features**: Includes global searching, per-column filtering, sorting, grouping, summary rows, and customizable pagination.
- 🤖 **Code Generation**: Comes with a `make:datatable` command to instantly scaffold your tables and a `tablenice:install` command to get set up quickly.
- 🛡️ **Type-Safe by Design**: Leverages modern PHP Enums for icons and colors, providing autocompletion and preventing common errors.
- 🧩 **Component-Based**: Built with a clean separation of concerns using Livewire and Blade components for maximum flexibility and reusability.
- 📱 **Fully Responsive**: Designed with a mobile-first approach to ensure a seamless experience on any device.

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
- Publish a `DatatablePage.php` Livewire component and its corresponding view. These are required to use the `--route` option in the `make:datatable` command.
- Automatically detect your Tailwind CSS version and provide appropriate configuration instructions.
- Provide you with clear, copy-pasteable instructions for the final manual steps.

### 3. Frontend Dependencies

The interactive elements of TableNice, like charts, calendars, and the rich text editor, require a few frontend packages. Install these via npm:

```bash
npm install -D chart.js dayjs litepicker trix @tailwindcss/typography @popperjs/core
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

### 4. Configure Tailwind CSS Typography Plugin

TableNice requires the `@tailwindcss/typography` plugin for rich text formatting. The configuration depends on your Tailwind CSS version:

#### For Tailwind CSS v4
Add the following line to your `resources/css/app.css` file:

```css
@plugin "@tailwindcss/typography";
```

#### For Tailwind CSS v3
Add the typography plugin to your `tailwind.config.js` file:

```javascript
module.exports = {
  // ... other config
  plugins: [
    require('@tailwindcss/typography'),
    // ... other plugins
  ],
}
```

> **Note**: The `tablenice:install` command will automatically detect your Tailwind version and provide the appropriate instructions.

### 5. Update Your Main Layout

For modals and alerts to work correctly across your entire application, you must add the global TableNice Livewire components to your main layout file (e.g., `resources/views/layouts/app.blade.php`). Place these tags just before the closing `</body>` tag.

```blade
    ...
    {{-- This allows TableNice to display alerts and modals anywhere in your app --}}
    <livewire:tablenice-alert />
    <livewire:tablenice-form-modal />
    <livewire:tablenice-confirmation-modal />
    @stack('tablenice-scripts')
    
    @livewireScripts
</body>
```

## Usage

### 1. Generate a Datatable

Use the `make:datatable` command to generate a Table and its corresponding Form class.

```bash
php artisan make:datatable UserTable --model=User --route
```

#### Available Command Options

- `--model=User`: (Required) The Eloquent model to use for the table. The command will inspect this model to pre-fill columns and form fields.
- `--theme=indigo`: Specifies a color theme for the generated table class. Defaults to blue.
- `--route`: Automatically adds a full-page route for this datatable in `routes/web.php` using the published DatatablePage component.
- `--force`: Overwrites existing Table and Form files if they already exist.

### 2. Customize Your Table

Open `app/DataTables/UserTable.php` to configure your columns, actions, and theme.

```php
<?php

namespace App\DataTables;

use Mystamyst\TableNice\Actions\CreateAction;
use Mystamyst\TableNice\Table;
use App\Models\User;
// ... other imports

class UserTable extends Table
{
    public string $model = User::class;

    public function columns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable(),

            // ... more columns
        ];
    }
    
    // ... actions, cards, etc.
}
```

### 3. Advanced Usage: Custom Query

You can easily modify the base query for your datatable by overriding the `query()` method in your Table class. This is perfect for adding complex joins or default scopes.

```php
use Illuminate\Contracts\Database\Eloquent\Builder;

public function query(): Builder
{
    // Start with the base model query and add your own logic
    return parent::query()
        ->with('posts')
        ->where('active', true);
}
```

### 4. Advanced Usage: API / Collection Data Source

TableNice is not limited to Eloquent models. You can source your data from anywhere by overriding the `data()` method. If this method returns a Collection, it will be used instead of the `query()` method.

```php
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

public function data(): ?Collection
{
    // Example: Fetching data from an external API
    $response = Http::get('https://api.example.com/users');
    
    // Ensure each item has a unique 'id' for actions to work
    return collect($response->json('data'));
}

// You no longer need the $model property
// public ?string $model = null; 
```

That's it! Visit your newly created route to see your datatable.