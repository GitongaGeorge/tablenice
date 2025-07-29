<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | This option controls the default number of items per page
    |
    */
    'per_page' => 10,
    
    /*
    |--------------------------------------------------------------------------
    | Per Page Options
    |--------------------------------------------------------------------------
    |
    | Available options for items per page
    |
    */
    'per_page_options' => [5, 10, 25, 50, 100],
    
    /*
    |--------------------------------------------------------------------------
    | Search Placeholder
    |--------------------------------------------------------------------------
    |
    | Default placeholder text for search input
    |
    */
    'search_placeholder' => 'Search...',
    
    
    'views' => [
        'layout' => 'tablenice::layouts.app', // Default layout for your Livewire components
    ],

    'pagination' => [
        'default_per_page' => 10,
        'per_page_options' => [10, 25, 50, 100],
        'view' => 'tablenice::pagination.tablenice', // Custom pagination view
    ],

    'modal' => [
        'default_width' => '2xl', // e.g., 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
        'default_close_button' => true,
    ],
];