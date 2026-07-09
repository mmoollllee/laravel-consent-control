<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;

/**
 * Renders a consent-gated <script type="text/plain" data-consent="…">. The script
 * stays inert until the visitor grants the category, then the runtime activates it.
 *
 *   <x-consent-control-script consent="analytics" src="https://…/gtag.js" />
 *
 *   <x-consent-control-script consent="analytics">
 *       window.dataLayer = window.dataLayer || [];
 *   </x-consent-control-script>
 */
class Script extends Component
{
    public string $consent;

    public ?string $src;

    public function __construct(string $consent, ?string $src = null)
    {
        $this->consent = $consent;
        $this->src = $src;
    }

    public function render()
    {
        return view('consent-control::components.script');
    }
}
