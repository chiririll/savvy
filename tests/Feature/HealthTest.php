<?php

it('reports liveness as health+json pass', function () {
    $response = $this->get('/livez');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/health+json');
    expect($response->json('status'))->toBe('pass');
});

it('reports readiness unavailable before migrations are applied', function () {
    $response = $this->get('/readyz');

    $response->assertStatus(503);
    expect($response->headers->get('Content-Type'))->toContain('application/health+json');
    expect($response->json('status'))->toBe('fail');
    expect($response->json('checks.schema:migrations.0.status'))->toBe('fail');
});

it('reports readiness pass once the schema is migrated', function () {
    $this->artisan('migrate', ['--force' => true]);

    $response = $this->get('/readyz');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/health+json');
    expect($response->json('status'))->toBe('pass');
    expect($response->json('checks.sqlite:connectivity.0.status'))->toBe('pass');
    expect($response->json('checks.schema:migrations.0.observedValue'))->toBe(0);
});
