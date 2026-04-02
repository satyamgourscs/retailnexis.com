<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Cache\TaggableStore;
use Stancl\Tenancy\CacheManager as StanclTenantCacheManager;

/**
 * Stancl's cache bootstrapper always uses tags; database/file drivers cannot tag.
 * This manager prefixes keys per tenant when the store is not taggable (same isolation goal).
 */
class TenantSafeCacheManager extends StanclTenantCacheManager
{
    public function __call($method, $parameters)
    {
        $repository = $this->store();
        $store = $repository->getStore();

        if ($store instanceof TaggableStore) {
            return parent::__call($method, $parameters);
        }

        if ($method === 'tags') {
            throw new \BadMethodCallException(
                'Cache tags require a tag-capable store (redis, memcached, etc.). Current store: '.get_class($store)
            );
        }

        $this->prefixParametersForTenant($method, $parameters);

        return $repository->$method(...$parameters);
    }

    /**
     * @param  array<int, mixed>  $parameters
     */
    protected function prefixParametersForTenant(string $method, array &$parameters): void
    {
        $tenant = tenant();
        if (! $tenant) {
            return;
        }

        $prefix = config('tenancy.cache.tag_base').$tenant->getTenantKey().':';

        $stringKeyMethods = [
            'get', 'has', 'missing', 'pull', 'put', 'set', 'add',
            'increment', 'decrement', 'forever', 'forget', 'delete',
            'remember', 'rememberForever', 'sear',
        ];

        if (in_array($method, $stringKeyMethods, true) && isset($parameters[0]) && is_string($parameters[0])) {
            $parameters[0] = $prefix.$parameters[0];

            return;
        }

        if ($method === 'many' && isset($parameters[0]) && is_array($parameters[0])) {
            $parameters[0] = array_map(
                static fn ($key) => is_string($key) ? $prefix.$key : $key,
                $parameters[0]
            );

            return;
        }

        if ($method === 'getMultiple' && isset($parameters[0]) && is_array($parameters[0])) {
            $parameters[0] = array_map(
                static fn ($key) => is_string($key) ? $prefix.$key : $key,
                $parameters[0]
            );

            return;
        }

        if (($method === 'putMany' || $method === 'setMultiple') && isset($parameters[0]) && is_array($parameters[0])) {
            $prefixed = [];
            foreach ($parameters[0] as $key => $value) {
                $prefixed[is_string($key) ? $prefix.$key : $key] = $value;
            }
            $parameters[0] = $prefixed;

            return;
        }

        if ($method === 'deleteMultiple' && isset($parameters[0]) && is_array($parameters[0])) {
            $parameters[0] = array_map(
                static fn ($key) => is_string($key) ? $prefix.$key : $key,
                $parameters[0]
            );
        }
    }
}
