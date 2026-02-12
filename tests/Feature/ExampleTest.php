<?php

test('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('fonts.bunny.net', false);
    $response->assertSee('instrument-sans:400,500,600', false);
});
