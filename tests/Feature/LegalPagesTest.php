<?php

it('renders legal pages', function (string $url) {
    $this->get($url)
        ->assertOk()
        ->assertSee('https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap', false)
        ->assertDontSee('https://fonts.bunny.net/files/instrument-sans-');
})->with([
    '/acceptable-use',
    '/cookies',
    '/privacy',
    '/terms',
]);
