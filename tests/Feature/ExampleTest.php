<?php

test('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('fonts.bunny.net', false);
    $response->assertSee('instrument-sans-latin-400-normal.woff2', false);
});
