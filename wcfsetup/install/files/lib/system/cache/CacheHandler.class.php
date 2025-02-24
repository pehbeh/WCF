<?php

namespace wcf\system\cache;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use wcf\system\cache\adapter\DiskCacheAdapter;
use wcf\system\cache\adapter\ICacheAdapter;
use wcf\system\SingletonFactory;

/**
 * Manages transparent cache access.
 *
 * @author  Olaf Braun, Alexander Ebert, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class CacheHandler extends SingletonFactory
{
    private TagAwareCacheInterface & TagAwareAdapterInterface $cache;

    /**
     * Creates a new CacheHandler object.
     */
    protected function init()
    {
        try {
            $className = 'wcf\system\cache\adapter\\' . \ucfirst(CACHE_SOURCE_TYPE) . 'CacheAdapter';
            if (!\is_subclass_of($className, ICacheAdapter::class)) {
                $className = DiskCacheAdapter::class;
            }

            $this->cache = $className::getAdapter();
        } catch (\Exception $e) {
            if (CACHE_SOURCE_TYPE != 'disk') {
                // fallback to disk cache
                $this->cache = DiskCacheAdapter::getAdapter();
            } else {
                throw $e;
            }
        }
    }

    /**
     * Flushes the entire cache.
     */
    public function flushAll(): void
    {
        $this->cache->clear();
    }

    /**
     * Returns the cache item for the given key.
     *
     * @template T of array|object
     *
     * @param ICacheCallback<T> $callback
     *
     * @return T
     */
    public function get(string $key, ICacheCallback $callback, bool $forceRebuild = false): array|object
    {
        return $this->cache->get($key, $callback, $forceRebuild ? \INF : null);
    }

    /**
     * Deletes the cache item for the given key.
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * Invalidates the cache items with the associated tags.
     *
     * @param list<string> $tags
     */
    public function invalidateTags(array $tags): bool
    {
        return $this->cache->invalidateTags($tags);
    }

    /**
     * Returns cache index hash.
     */
    public function getCacheIndex(array $parameters): string
    {
        return \sha1(\serialize($this->orderParameters($parameters)));
    }

    /**
     * Returns the cache adapter.
     */
    public function getCacheAdapter(): TagAwareCacheInterface & TagAwareAdapterInterface
    {
        return $this->cache;
    }

    /**
     * Unifies parameter order, numeric indices will be discarded.
     *
     * @param array $parameters
     * @return  array
     */
    protected function orderParameters($parameters)
    {
        if (!empty($parameters)) {
            \array_multisort($parameters);
        }

        return $parameters;
    }

    /**
     * Returns false, if the configured cache source type could not be initialized.
     *
     * @since 6.1
     */
    public function sanityCheck(): bool
    {
        if (
            CACHE_SOURCE_TYPE != 'disk'
            && \get_class($this->cache) === FilesystemTagAwareAdapter::class
        ) {
            return false;
        }

        return true;
    }
}
