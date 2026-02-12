<?php

use App\Models\Package;
use App\Models\User;

it('redirects unverified users from subscription mutation routes', function (string $routeName) {
    $user = User::factory()->unverified()->create();
    $package = Package::factory()->create();

    $this->actingAs($user)
        ->post(route($routeName, $package))
        ->assertRedirect(route('verification.notice'));
})->with([
    'subscribe route' => 'packages.subscribe',
    'swap route' => 'packages.swap',
]);
