<?php

namespace Mmoollllee\LaravelConsentControl;

use Illuminate\Support\Facades\Blade;
use Mmoollllee\LaravelConsentControl\View\Components\Banner;
use Mmoollllee\LaravelConsentControl\View\Components\Gate;
use Mmoollllee\LaravelConsentControl\View\Components\Message;
use Mmoollllee\LaravelConsentControl\View\Components\Script;
use Mmoollllee\LaravelConsentControl\View\Components\Scripts;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelConsentControlServiceProvider extends PackageServiceProvider
{
    public static string $name = 'consent-control';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasAssets();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ConsentControlManager::class);
    }

    public function packageBooted(): void
    {
        // Frontend Blade components: <x-consent-control-banner />, -message, -gate, -scripts
        $this->loadViewComponentsAs('consent-control', [
            Banner::class,
            Message::class,
            Gate::class,
            Script::class,
            Scripts::class,
        ]);

        // Convenience directives.
        Blade::directive('consentBanner', fn () => "<?php echo view('consent-control::components.banner')->render(); ?>");
        Blade::directive('consentScripts', fn () => "<?php echo view('consent-control::components.scripts')->render(); ?>");

        // Server-side gate: @consent('analytics') ... @endconsent
        Blade::if('consent', fn (string $category) => app(ConsentControlManager::class)->granted($category));
    }
}
