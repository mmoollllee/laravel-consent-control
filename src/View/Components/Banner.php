<?php

namespace Mmoollllee\LaravelConsentControl\View\Components;

use Illuminate\View\Component;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;

class Banner extends Component
{
    public array $categories;

    public array $banner;

    public array $links;

    public function __construct()
    {
        $manager = app(ConsentControlManager::class);
        $this->categories = $manager->getCategories();
        $this->banner = $manager->getBannerConfig();
        $this->links = $manager->getLinks();
    }

    public function render()
    {
        return view('consent-control::components.banner');
    }
}
