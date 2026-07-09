<?php

namespace Mmoollllee\LaravelConsentControl;

use Mmoollllee\LaravelConsentControl\Contracts\ConfigDriverInterface;
use Mmoollllee\LaravelConsentControl\Drivers\ConfigFileDriver;
use Mmoollllee\LaravelConsentControl\Drivers\EloquentDriver;

class ConsentControlManager
{
    protected ConfigDriverInterface $driver;

    public function __construct()
    {
        $this->driver = $this->resolveDriver();
    }

    public function getCategories(): array
    {
        return $this->driver->getCategories();
    }

    public function getCookieConfig(): array
    {
        return $this->driver->getCookieConfig();
    }

    public function getBannerConfig(): array
    {
        return $this->driver->getBannerConfig();
    }

    public function getLinks(): array
    {
        return $this->driver->getLinks();
    }

    public function getAllConfig(): array
    {
        return $this->driver->getAllConfig();
    }

    public function save(array $data): void
    {
        $this->driver->save($data);
    }

    public function getDriver(): ConfigDriverInterface
    {
        return $this->driver;
    }

    public function cookieName(): string
    {
        return $this->getCookieConfig()['name'] ?? 'consentcontrol';
    }

    /**
     * Server-side check whether the visitor granted a consent category.
     *
     * Reads the plaintext consent cookie directly from $_COOKIE so it is not
     * affected by Laravel's cookie encryption (the cookie is written by the
     * client-side JS runtime, never by Laravel).
     */
    public function granted(string $category): bool
    {
        $raw = $_COOKIE[$this->cookieName()] ?? '';

        if (! is_string($raw) || $raw === '') {
            return false;
        }

        return in_array($category, explode('|', $raw), true);
    }

    protected function resolveDriver(): ConfigDriverInterface
    {
        return match (config('consent-control.driver', 'config')) {
            'eloquent' => new EloquentDriver,
            default => new ConfigFileDriver,
        };
    }
}
