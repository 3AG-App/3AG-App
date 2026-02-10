<?php

it('renders legal pages', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/acceptable-use',
    '/cookies',
    '/privacy',
    '/terms',
]);
