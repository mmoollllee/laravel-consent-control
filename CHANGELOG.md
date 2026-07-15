# Changelog

All notable changes to `laravel-consent-control` will be documented in this file.

## 0.1.3 - 2026-07-15

No more banner flash for returning visitors (ships
[`consent-control`](https://github.com/mmoollllee/consent-control) 2.1.1).

- `<x-consent-control-banner>` now ships with an inline `display:none`;
  visibility is JS-only (the runtime shows it on missing/stale consent and via
  reopen buttons). Previously the hidden state depended on the (Tailwind or
  fallback) stylesheet, so the banner could flash on load even though consent
  was already given (Vite dev server, late-loading stylesheet, cached pages).
  Side benefit: the hidden banner no longer sits focusable off-screen in the
  tab order.
- Bundled runtime updated: `ConsentControl.show()` clears the inline style
  (with a reflow) so the slide-in transition still runs.

## 0.1.2 - 2026-07-13

- Allow Laravel 13 (`illuminate/contracts` + `illuminate/support`
  `^11.28|^12.0|^13.0`).

## 0.1.1 - 2026-07-10

Site-inheriting banner and a bring-your-own-bundle asset path (requires
[`consent-control`](https://github.com/mmoollllee/consent-control) 2.1).

- The banner Blade is now styled entirely with Tailwind utilities and `.btn`
  component classes, so it inherits the host site's design tokens and buttons.
  Add the package views to your `@source`.
- `<x-consent-control-scripts :assets="false" />` emits only the inline boot
  config — bundle the runtime JS + overlay CSS yourself (Vite import from the
  vendor `dist`). The published-asset path (default) still works, with
  `:standalone-css="false"` for Tailwind or a small neutral fallback otherwise.
- CSS split: always-loaded `consent-message.css` (overlay only) + the banner
  fallback stylesheet (from `resources/css/consent-control-fallback.css`).
- Privacy/imprint links moved out of the banner description into their own
  translation keys, shown only in the expanded panel.
- Reopen: `.consent-control--open` buttons reopen the banner expanded.
- Message overlay buttons carry `.btn btn-primary` to match the host site.

## 0.1.0 - 2026-07-09

Initial release. Laravel/Blade layer on top of the framework-agnostic
[`consent-control`](https://github.com/mmoollllee/consent-control) JS runtime.

- Server-rendered Blade components: `<x-consent-control-banner>`, `<x-consent-control-message>`,
  `<x-consent-control-gate>`, `<x-consent-control-scripts>`.
- `@consentBanner`, `@consentScripts` and `@consent('category') … @endconsent` directives.
- `config`/`eloquent` drivers — a single config file is enough; the database driver
  is opt-in for runtime editing.
- Multilingual category labels (DE/EN) resolved through `__()`.
- `ConsentControl::granted()` server-side helper.
- Consent versioning (`version` config): visitors are re-prompted when the policy changes.
- `<x-consent-control-script>` for consent-gated `<script>` tags (activated on consent).
