<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;

class Scripts extends Component
{
    public bool $standaloneCss;

    public array $initConfig;

    public function __construct(bool $standaloneCss = true)
    {
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
            'cookieDomain' => $cookie['domain'] ?? null,
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

    public function render()
    {
        return view('consent-control::components.scripts');
    }
}
