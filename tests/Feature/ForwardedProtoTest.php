<?php

use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/*
 * AppServiceProvider::boot() форсит https-схему генерируемых URL, когда
 * фронт-прокси прокидывает заголовок X-Forwarded-Proto: https. Провайдер бутим
 * вручную с подменённым request, потому что в feature-тестах boot() отрабатывает
 * один раз на старте приложения — до того, как $this->get() создаст запрос с
 * заголовками, и кернел уже забутстрапленное приложение повторно не бутит.
 */

function bootProviderWithForwardedProto(?string $proto): void
{
    $request = Request::create(
        'http://app.test/dashboard',
        'GET',
        server: $proto !== null ? ['HTTP_X_FORWARDED_PROTO' => $proto] : [],
    );

    // Подмена request триггерит rebinding-колбэк роутинга, который обновляет
    // request внутри уже созданного UrlGenerator.
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

/*
 * Реальные сетапы с цепочкой балансировщик -> nginx прокидывают
 * X-Forwarded-Proto списком (RFC 7239 / поведение nginx по умолчанию).
 * Первый хоп здесь — https, значит исходный клиент пришёл по https и схему
 * нужно форсить. Строгое сравнение `=== 'https'` в провайдере этого не ловит.
 */
it('forces the https scheme when X-Forwarded-Proto is a proxy chain starting with https', function () {
    bootProviderWithForwardedProto('https, http');

    expect(URL::to('/transactions'))->toStartWith('https://');
});

/*
 * Заголовок регистронезависим (RFC 9110). Прокси, отдающий `HTTPS`,
 * семантически идентичен `https`, но строгое сравнение его теряет.
 */
it('forces the https scheme when X-Forwarded-Proto casing differs', function () {
    bootProviderWithForwardedProto('HTTPS');

    expect(URL::to('/transactions'))->toStartWith('https://');
});
