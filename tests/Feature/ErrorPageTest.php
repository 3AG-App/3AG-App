<?php

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the custom inertia error page for not found routes', function () {
    $this->withoutVite();

    $this->get('/this-route-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('error')
            ->where('status', 404)
        );
});

it('flashes a toast and redirects back on 419 responses', function () {
    Route::middleware('web')->get('/__test-419', function () {
        abort(419);
    });

    Route::middleware('web')->get('/__test-source', function () {
        return 'ok';
    });

    $this->from('/__test-source')
        ->get('/__test-419')
        ->assertRedirect('/__test-source')
        ->assertSessionHas('inertia.flash_data.toast.type', 'error')
        ->assertSessionHas('inertia.flash_data.toast.message', 'The page expired, please try again.');
});
