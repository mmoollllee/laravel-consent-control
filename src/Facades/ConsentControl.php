<?php

namespace Mmoollllee\LaravelConsentControl\Facades;

use Illuminate\Support\Facades\Facade;
use Mmoollllee\LaravelConsentControl\ConsentControlManager;

/**
 * @method static array getCategories()
 * @method static array getCookieConfig()
 * @method static array getBannerConfig()
 * @method static array getLinks()
 * @method static array getAllConfig()
 * @method static void save(array $data)
 * @method static string cookieName()
 * @method static bool granted(string $category)
 * @method static \Mmoollllee\LaravelConsentControl\Contracts\ConfigDriverInterface getDriver()
 *
 * @see ConsentControlManager
 */
class ConsentControl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ConsentControlManager::class;
    }
}
