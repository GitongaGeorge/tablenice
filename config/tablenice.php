<?php

// mystamyst/tablenice configuration
return [

    /*
    |--------------------------------------------------------------------------
    | Search Placeholder
    |--------------------------------------------------------------------------
    |
    | This value will be used as the default placeholder text for the main
    | search input on the datatable.
    |
    */
    'search_placeholder' => 'Search Records...',

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Configure the default pagination behavior for all datatables. These
    | values are used to populate the 'items per page' dropdown and set
    | the initial number of records to display.
    |
    */
    'pagination' => [
        'default_per_page' => 10,
        'per_page_options' => [10, 25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modal Settings
    |--------------------------------------------------------------------------
    |
    | Define the default appearance and behavior of modals that are opened
    | by TableNice actions.
    |
    */
    'modal' => [
        // Options: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl'
        'default_width' => '2xl',
    ],
];

