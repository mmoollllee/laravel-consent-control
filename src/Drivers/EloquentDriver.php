<?php

namespace Mmoollllee\LaravelConsentControl\Drivers;

use Mmoollllee\LaravelConsentControl\Contracts\ConfigDriverInterface;

/**
 * Stores/reads consent settings as JSON on one of your own Eloquent models.
 *
 * Opt-in: set CONSENT_CONTROL_DRIVER=eloquent and point consent-control.eloquent
 * at the model/field/record that holds the settings. Falls back to the config
 * file for any key that is not present in the database.
 */
class EloquentDriver implements ConfigDriverInterface
{
    protected ?array $cached = null;

    public function getCategories(): array
    {
        return $this->resolve('categories');
    }

    public function getCookieConfig(): array
    {
        return $this->resolve('cookie');
    }

    public function getBannerConfig(): array
    {
        return $this->resolve('banner');
    }

    public function getLinks(): array
    {
        return $this->resolve('links');
    }

    public function getAllConfig(): array
    {
        $db = $this->getDbData();

        return [
            'cookie' => $db['cookie'] ?? config('consent-control.cookie', []),
            'banner' => $db['banner'] ?? config('consent-control.banner', []),
            'categories' => $this->normalizeCategories($db['categories'] ?? null),
            'links' => $db['links'] ?? config('consent-control.links', []),
        ];
    }

    public function save(array $data): void
    {
        $modelClass = config('consent-control.eloquent.model');
        $field = config('consent-control.eloquent.field', 'consent_settings');
        $recordId = config('consent-control.eloquent.record_id', 1);

        $record = $modelClass::findOrFail($recordId);
        $record->{$field} = $data;
        $record->save();

        $this->cached = null;
    }

    protected function resolve(string $key): array
    {
        $db = $this->getDbData();

        if ($key === 'categories') {
            return $this->normalizeCategories($db['categories'] ?? null);
        }

        return $db[$key] ?? config("consent-control.{$key}", []);
    }

    protected function getDbData(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        try {
            $modelClass = config('consent-control.eloquent.model');
            $field = config('consent-control.eloquent.field', 'consent_settings');
            $recordId = config('consent-control.eloquent.record_id', 1);

            $record = $modelClass::find($recordId);
            $this->cached = $record?->{$field} ?? [];
        } catch (\Throwable $e) {
            $this->cached = [];
        }

        return $this->cached;
    }

    /**
     * Accept both keyed (`['analytics' => [...]]`) and list-style
     * (`[['key' => 'analytics', ...]]`) category data so it works with a
     * Filament Repeater as well as a hand-written config array.
     */
    protected function normalizeCategories(?array $raw): array
    {
        if (! $raw) {
            return config('consent-control.categories', []);
        }

        $firstKey = array_key_first($raw);
        if (is_string($firstKey)) {
            return $raw;
        }

        $result = [];
        foreach ($raw as $category) {
            if (isset($category['key'])) {
                $result[$category['key']] = $category;
            }
        }

        return $result ?: config('consent-control.categories', []);
    }
}
