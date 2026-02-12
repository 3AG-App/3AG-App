<?php

use Inertia\Testing\AssertableInertia as Assert;

it('shows the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('home'));
});
