<?php

use Illuminate\Http\Request;

it('does not error when X-Forwarded-Host is empty', function () {
    $this->get('/', ['X-Forwarded-Host' => ''])
        ->assertOk();
});

it('does not error when X-Forwarded-Host is spoofed', function () {
    $this->get('/', ['X-Forwarded-Host' => 'evil.example.com'])
        ->assertOk();
});

it('does not trust a spoofed X-Forwarded-Host', function () {
    $this->get('/');

    $request = Request::create('http://real.test/x', 'GET', server: [
        'HTTP_X_FORWARDED_HOST' => 'evil.example.com',
    ]);

    expect($request->getHost())->toBe('real.test');
});

it('does not error on a multi-hop X-Forwarded-Proto chain', function () {
    $this->get('/', ['X-Forwarded-Proto' => 'https, http'])
        ->assertOk();
});
