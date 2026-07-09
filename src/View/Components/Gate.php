<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;

class Gate extends Component
{
    public string $consent;

    public bool $granted;

    public function __construct(string $consent)
    {
        $this->consent = $consent;
        $this->granted = app(ConsentControlManager::class)->granted($consent);
    }

    public function render()
    {
        return view('consent-control::components.gate');
    }
}
