<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Email Addresses
    |--------------------------------------------------------------------------
    |
    | These email addresses are granted access to the Filament admin panel.
    | Add additional admin emails to this array or set the ADMIN_EMAILS
    | environment variable as a comma-separated list.
    |
    */

    'emails' => array_filter(array_map(
        'trim',
        explode(',', env('ADMIN_EMAILS', ''))
    )),

];
