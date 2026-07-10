# Laravel Consent Control

GDPR cookie-consent banner and content blocking for Laravel — server-rendered Blade
on top of the framework-agnostic [`consent-control`](https://github.com/mmoollllee/consent-control)
JavaScript runtime.

This is the **Laravel layer** of a three-package stack:

| Package | Role |
|---|---|
| [`consent-control`](https://github.com/mmoollllee/consent-control) (npm) | Framework-agnostic runtime + CSS (plain HTML, WordPress, any framework). |
| **`laravel-consent-control`** (this package) | Blade components, config, drivers, translations, server helpers. |
| [`filament-consent-control`](https://github.com/mmoollllee/filament-consent-control) | Optional Filament admin UI (settings form + RichEditor iframe plugin). |

A single config file is all you need — no database, no admin panel required.

## Features

- Server-rendered banner (no flash, SEO-friendly) wired by the shared JS runtime
- Configurable consent categories with declarative scripts / inline JS on consent
- Iframe & custom-content blocking via `<x-consent-control-message>`
- Conditional rendering via `<x-consent-control-gate>` (server-side + reactive)
- `config` or `eloquent` driver — store settings in a file or on your own model
- Multilingual (DE/EN, extensible) category labels resolved through `__()`
- Server-side `ConsentControl::granted('analytics')` helper
- Cookie format compatible with the `consent-control` npm package: `consentcontrol=necessary|analytics`

## Requirements

- PHP 8.2+
- Laravel 11.28+ / 12

## Installation

```bash
composer require mmoollllee/laravel-consent-control
```

Publish the config and the runtime assets:

```bash
php artisan vendor:publish --tag=consent-control-config
php artisan vendor:publish --tag=consent-control-assets
```

Optionally publish the translations to customise them:

```bash
php artisan vendor:publish --tag=consent-control-translations
```

## Frontend usage

Render the banner and load the runtime (once per page, e.g. before `</body>`):

```blade
<x-consent-control-banner />
<x-consent-control-scripts />
```

`<x-consent-control-scripts />` ships a standalone stylesheet by default. If you bundle
the CSS yourself (see [Assets](#assets)) disable it with `:standalone-css="false"`.

### Block an iframe until consent

```blade
<x-consent-control-message
    consent="functional"
    src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"
    src-name="YouTube"
    :width="560"
    :height="315"
/>
```

### Block custom content until consent

```blade
<x-consent-control-message consent="functional" type="custom" src-name="OpenStreetMap">
    <div id="map" style="height: 400px"></div>
</x-consent-control-message>
```

### Conditional content

```blade
<x-consent-control-gate consent="analytics">
    <p>Only rendered/visible when analytics consent is granted.</p>
</x-consent-control-gate>

{{-- Or server-side only: --}}
@consent('analytics')
    <x-analytics-widget />
@endconsent
```

### Re-open the banner

```blade
<button class="consent-control--open">Cookie settings</button>
```

## Configuration

Everything lives in `config/consent-control.php`. Labels and descriptions may be plain
strings **or** translation keys — both are resolved through `__()`, so the shipped
defaults are multilingual out of the box.

Declare the scripts/JS to run when a category is granted:

```php
'analytics' => [
    'label' => 'Analytics',
    'scripts' => [
        ['src' => 'https://www.googletagmanager.com/gtag/js?id=G-XXXX', 'async' => true],
    ],
    'inline_script' => "window.dataLayer = window.dataLayer || [];",
],
```

### Consent versioning

Bump `version` whenever your categories or privacy policy change — visitors whose stored
version differs are re-prompted (their consent is reset):

```php
'version' => env('CONSENT_CONTROL_VERSION', 1), // set null to disable
```

### Block your own scripts

Render inert scripts that activate only on consent:

```blade
<x-consent-control-script consent="analytics" src="https://www.googletagmanager.com/gtag/js?id=G-XXXX" />

<x-consent-control-script consent="analytics">
    window.dataLayer = window.dataLayer || [];
</x-consent-control-script>
```

### Eloquent driver (opt-in)

Store settings as JSON on one of your own models instead of the config file:

```env
CONSENT_CONTROL_DRIVER=eloquent
```

```php
// config/consent-control.php
'eloquent' => [
    'model' => App\Models\Setting::class,
    'field' => 'consent_settings',
    'record_id' => 1,
],
```

```php
// The model needs a JSON cast:
protected $casts = ['consent_settings' => 'array'];
```

Editing that JSON with a ready-made Filament form is what
[`filament-consent-control`](https://github.com/mmoollllee/filament-consent-control) adds.

## Frontend assets (choose one)

Render the banner and the boot component once per page (e.g. before `</body>`):

```blade
<x-consent-control-banner />
<x-consent-control-scripts :assets="false" />
```

**The banner is designed to inherit your site's design** — pick the path that fits your setup:

**A) Bundle it yourself — recommended.** The runtime JS and the overlay CSS become part of
your Vite build (no extra requests, full control), and Tailwind styles the banner Blade: it
adopts your design tokens (`gray` scale, the toggle uses your `--color-primary`) and your
button components (`.btn`, `.btn-primary`, `.btn-secondary`) automatically.

```js
// resources/js/app.js
import '../../vendor/mmoollllee/laravel-consent-control/resources/dist/js/consent-control.js';
```

```css
/* resources/css/app.css */
@import '../../vendor/mmoollllee/laravel-consent-control/resources/dist/css/consent-message.css';
@source '../../vendor/mmoollllee/laravel-consent-control/resources/views/components/**/*.blade.php';
```

With `:assets="false"` the component emits only the inline `ConsentControl.init(...)` boot
config. (Alternatively `npm i consent-control` and import from node_modules — same runtime.)

**B) Published assets — no build step.** Publish the runtime and let the component load it:

```bash
php artisan vendor:publish --tag=consent-control-assets   # → public/vendor/consent-control
```

```blade
<x-consent-control-scripts />
```

Tailwind projects still add the `@source` line above and pass `:standalone-css="false"`
(the Blade styles the banner). Projects **without** a utility/button system keep the default:
a small neutral stylesheet styles banner + overlay buttons and still adopts your brand where
it can — the accent reads `--color-primary` / `--bs-primary`, and every `--cc-*` variable can
be overridden:

```css
#consent-control-banner, .consent-message { --cc-primary: #b91c1c; }
```

**C) Full control.** Publish the views and make them yours — the runtime only needs its
selector contract (`#consent-control-banner`, `input[value=<category>]`,
`#consent-control--submit(-all)`, `.consent-control--open/--close/--reset`,
`.collapsed-only`/`.uncollapsed-only` and the `hide`/`is-collapsed` state classes):

```bash
php artisan vendor:publish --tag=consent-control-views
```

## Reopening the banner

Visitors must be able to change their choice later (GDPR). Place a button with the
`consent-control--open` class anywhere — typically on the privacy policy page; the runtime
binds every such element on init and reopens the banner with the settings expanded:

```html
<button type="button" class="consent-control--open">Cookie-Einstellungen ändern</button>
```

## Components & directives

| Component / directive | Description |
|---|---|
| `<x-consent-control-banner />` / `@consentBanner` | Consent banner |
| `<x-consent-control-message consent="…" src="…" />` | Iframe/content with consent overlay |
| `<x-consent-control-gate consent="…">` | Slot shown only on consent |
| `<x-consent-control-script consent="…" src="…" />` | Consent-gated `<script>` tag |
| `<x-consent-control-scripts />` / `@consentScripts` | Runtime + boot config |
| `@consent('analytics') … @endconsent` | Server-side conditional |

## JavaScript events

`window` receives a `consent-updated` event whenever consent changes:

```js
window.addEventListener('consent-updated', (e) => console.log(e.detail.consents));
```

## License

MIT. See [LICENSE.md](LICENSE.md).
