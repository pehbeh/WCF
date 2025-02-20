<?php

namespace wcf\system\cache\eager;

use wcf\system\cache\CacheHandler;

/**
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @template T of array|object
 */
abstract class AbstractEagerCache
{
    /**
     * @var array<string, T>
     */
    private static array $caches = [];
    private string $cacheName;

    /**
     * Returns the cache.
     *
     * @return T
     */
    final public function getCache(): array | object
    {
        $key = $this->getCacheKey();

        if (!\array_key_exists($key, AbstractEagerCache::$caches)) {
            $cache = CacheHandler::getInstance()->getCacheSource()->get($key, 0);
            if ($cache === null) {
                $this->rebuild();
            } else {
                AbstractEagerCache::$caches[$key] = $cache;
            }
        }

        return AbstractEagerCache::$caches[$key];
    }

    private function getCacheKey(): string
    {
        if (!isset($this->cacheName)) {
            /* @see CacheHandler::getCacheName() */
            $reflection = new \ReflectionClass($this);
            $this->cacheName = \str_replace(
                ['\\', 'system_cache_eager_'],
                ['_', ''],
                \get_class($this)
            );

            $parameters = [];
            foreach ($reflection->getProperties() as $property) {
                $parameters[$property->getName()] = $property->getValue($this);
            }
            if ($parameters !== []) {
                $this->cacheName .= '-' . CacheHandler::getInstance()->getCacheIndex($parameters);
            }
        }

        return $this->cacheName;
    }

    /**
     * Rebuilds the cache data and stores the updated data.
     */
    final public function rebuild(): void
    {
        $key = $this->getCacheKey();
        AbstractEagerCache::$caches[$key] = $this->getCacheData();
        CacheHandler::getInstance()->getCacheSource()->set($key, AbstractEagerCache::$caches[$key], 0);
    }

    /**
     * Generates the cache data and returns it.
     * This method MUST NOT rely on any (runtime) cache at any point because those could be stale.
     *
     * @return T
     */
    abstract protected function getCacheData(): array | object;
}
