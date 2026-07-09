<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;

class Message extends Component
{
    public string $consent;

    public ?string $src;

    public string $srcName;

    public string $type;

    public ?int $width;

    public ?int $height;

    public string $privacyUrl;

    public function __construct(
        string $consent,
        ?string $src = null,
        ?string $srcName = null,
        string $type = 'iframe',
        ?int $width = null,
        ?int $height = null,
    ) {
        $this->consent = $consent;
        $this->src = $src;
        $this->srcName = $srcName ?: ($src ? (parse_url($src, PHP_URL_HOST) ?: 'extern') : 'extern');
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
        $this->privacyUrl = config('consent-control.links.privacy', '/datenschutz/');
    }

    public function render()
    {
        return view('consent-control::components.message');
    }
}
