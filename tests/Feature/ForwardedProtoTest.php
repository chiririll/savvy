<?php

use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

function bootProviderWithForwardedProto(?string $proto): void
{
    $request = Request::create(
        'http://app.test/dashboard',
        'GET',
        server: $proto !== null ? ['HTTP_X_FORWARDED_PROTO' => $proto] : [],
    );

    app()->instance('request', $request);

    (new AppServiceProvider(app()))->boot();
}

it('forces the https scheme when X-Forwarded-Proto is https', function () {
    bootProviderWithForwardedProto('https');

    expect(URL::to('/transactions'))->toStartWith('https://');
});

it('keeps the http scheme when X-Forwarded-Proto is http', function () {
    bootProviderWithForwardedProto('http');

    expect(URL::to('/transactions'))->toStartWith('http://');
});

it('keeps the http scheme when X-Forwarded-Proto is absent', function () {
    bootProviderWithForwardedProto(null);

    expect(URL::to('/transactions'))->toStartWith('http://');
});

it('forces the https scheme when X-Forwarded-Proto is a proxy chain starting with https', function () {
    bootProviderWithForwardedProto('https, http');

    expect(URL::to('/transactions'))->toStartWith('https://');
});

it('forces the https scheme when X-Forwarded-Proto casing differs', function () {
    bootProviderWithForwardedProto('HTTPS');

    expect(URL::to('/transactions'))->toStartWith('https://');
});
