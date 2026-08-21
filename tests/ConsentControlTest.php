<?php

use Illuminate\Support\Facades\Blade;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;
use Mmoollllee\LaravelConsentControl\Drivers\ConfigFileDriver;
use Mmoollllee\LaravelConsentControl\View\Components\Scripts;

it('uses the config file driver by default', function () {
    expect(app(ConsentControlManager::class)->getDriver())->toBeInstanceOf(ConfigFileDriver::class);
});

it('ships default categories', function () {
    $categories = app(ConsentControlManager::class)->getCategories();

    expect($categories)->toHaveKeys(['necessary', 'analytics', 'functional']);
});

it('reads granted categories from the cookie', function () {
    $_COOKIE['consentcontrol'] = 'necessary|analytics';

    $manager = app(ConsentControlManager::class);

    expect($manager->granted('analytics'))->toBeTrue()
        ->and($manager->granted('functional'))->toBeFalse();

    unset($_COOKIE['consentcontrol']);
});

it('renders the banner with the runtime selector contract', function () {
    $html = Blade::render('<x-consent-control-banner />');

    expect($html)
        ->toContain('id="consent-control-banner"')
        ->toContain('id="consent-control--submit"')
        ->toContain('id="consent-control--submit-all"')
        ->toContain('value="necessary"');
});

it('renders the banner hidden until the runtime actively shows it', function () {
    expect(Blade::render('<x-consent-control-banner />'))->toContain('style="display: none"');
});

it('translates category labels per locale', function () {
    app()->setLocale('en');
    expect(Blade::render('<x-consent-control-banner />'))->toContain('Necessary');

    app()->setLocale('de');
    expect(Blade::render('<x-consent-control-banner />'))->toContain('Notwendige');
});

it('maps inline_script to the runtime inlineScript key', function () {
    config()->set('consent-control.categories.analytics.inline_script', 'console.log(1)');

    $config = (new Scripts)->initConfig;

    expect($config['categories']->analytics->inlineScript)->toBe('console.log(1)');
});

it('renders a blocked iframe via data-src', function () {
    $html = Blade::render(
        '<x-consent-control-message consent="functional" src="https://example.com/embed" src-name="Example" />'
    );

    expect($html)
        ->toContain('consent-message--wrapper')
        ->toContain('data-consent="functional"')
        ->toContain('data-src="https://example.com/embed"')
        // no real (leading-space) src attribute → the iframe stays blocked until consent
        ->not->toContain(' src="https://example.com/embed"');
});

it('includes the consent version in the boot config', function () {
    expect((new Scripts)->initConfig['version'])->toBe(1);
});

it('renders a consent-gated script tag', function () {
    $html = Blade::render('<x-consent-control-script consent="analytics" src="https://example.com/a.js" />');

    expect($html)
        ->toContain('type="text/plain"')
        ->toContain('data-consent="analytics"')
        ->toContain('src="https://example.com/a.js"');
});

it('keeps a cookie domain the current host belongs to', function () {
    config()->set('consent-control.cookie.domain', 'example.com');

    // Apex and subdomain both sit under the configured domain, so the widening
    // to cross-subdomain consent is preserved.
    foreach (['example.com', 'www.example.com'] as $host) {
        app('request')->headers->set('host', $host);

        expect((new Scripts)->initConfig['cookieDomain'])->toBe('example.com');
    }
});

it('drops a cookie domain the current host is not part of', function () {
    // One Laravel serving many customer domains: app.url has nothing to do with
    // the request host, and a pinned domain would be rejected by the browser.
    config()->set('consent-control.cookie.domain', 'nest.example.com');
    app('request')->headers->set('host', 'cam5.customer.test');

    expect((new Scripts)->initConfig['cookieDomain'])->toBeNull();
});

it('emits no cookie domain when none is configured', function () {
    config()->set('consent-control.cookie.domain', null);

    expect((new Scripts)->initConfig['cookieDomain'])->toBeNull();
});
