<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;

class Scripts extends Component
{
    /**
     * Emit <link>/<script src> tags for the published runtime assets. Set false
     * when you bundle the runtime + CSS yourself (Vite): the component then only
     * emits the inline ConsentControl.init() boot config.
     */
    public bool $assets;

    public bool $standaloneCss;

    public array $initConfig;

    public function __construct(bool $standaloneCss = true, bool $assets = true)
    {
        $this->assets = $assets;
        $this->standaloneCss = $standaloneCss;
        $this->initConfig = $this->buildInitConfig(app(ConsentControlManager::class));
    }

    /**
     * Build the object passed to ConsentControl.init(). Maps the PHP config
     * (snake_case, declarative services) to the JS runtime's expected shape.
     */
    protected function buildInitConfig(ConsentControlManager $manager): array
    {
        $cookie = $manager->getCookieConfig();
        $banner = $manager->getBannerConfig();
        $links = $manager->getLinks();

        $categories = [];
        foreach ($manager->getCategories() as $key => $cat) {
            $entry = [];

            if (! empty($cat['disabled'])) {
                $entry['disabled'] = true;
            }
            if (! empty($cat['checked'])) {
                $entry['checked'] = true;
            }
            if (! empty($cat['scripts'])) {
                $entry['scripts'] = array_values($cat['scripts']);
            }
            $inline = $cat['inline_script'] ?? $cat['inlineScript'] ?? null;
            if (! empty($inline)) {
                $entry['inlineScript'] = $inline;
            }

            // Cast to object so an empty service set still serialises as {} (not []).
            $categories[$key] = (object) $entry;
        }

        return [
            'version' => config('consent-control.version'),
            'cookieName' => $cookie['name'] ?? 'consentcontrol',
            'cookieDays' => (int) ($cookie['days'] ?? 365),
            'cookieDomain' => $this->resolveCookieDomain($cookie['domain'] ?? null),
            'cookiePath' => $cookie['path'] ?? '/',
            'cookieSameSite' => $cookie['same_site'] ?? 'lax',
            'cookieSecure' => (bool) ($cookie['secure'] ?? false),
            'rejectButton' => (bool) ($banner['reject_button'] ?? false),
            'categories' => (object) $categories,
            // Used by the runtime to generate the overlay for messages that are
            // not server-rendered with one (e.g. RichEditor-embedded iframes).
            // `{srcName}` is substituted client-side with the source name.
            'messageStrings' => [
                'button' => __('consent-control::consent.message.button'),
                'text' => __('consent-control::consent.message.text', [
                    'source' => '<i class="consent-message--source">{srcName}</i>',
                    'privacy_url' => $links['privacy'] ?? '/datenschutz/',
                ]),
            ],
        ];
    }

    /**
     * A cookie domain the current host is not part of is rejected by the
     * browser outright, so consent would never persist. The shipped default
     * derives the domain from `app.url`, which is correct for a single-site
     * app but wrong for one Laravel serving many customer domains: there the
     * request host has nothing to do with `app.url`. Drop the domain in that
     * case and let the runtime scope the cookie to the request host instead.
     *
     * An explicit, matching domain is kept, so opting into cross-subdomain
     * consent (`example.com` covering `www.example.com`) keeps working.
     */
    protected function resolveCookieDomain(?string $domain): ?string
    {
        if (blank($domain)) {
            return null;
        }

        $domain = ltrim($domain, '.');
        $host = request()?->getHost();

        if (blank($host)) {
            return $domain;
        }

        return $host === $domain || str_ends_with($host, '.'.$domain)
            ? $domain
            : null;
    }

    public function render()
    {
        return view('consent-control::components.scripts');
    }
}
