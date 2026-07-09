# Changelog

All notable changes to `laravel-consent-control` will be documented in this file.

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
