<?php

namespace wcf\system\cache\eager;

use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\cache\CacheHandler;
use wcf\system\cache\ICacheCallback;
use wcf\util\ClassUtil;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @template T of array|object
 * @implements ICacheCallback<T>
 */
abstract class AbstractEagerCache implements ICacheCallback
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
    final public function get(): array|object
    {
        $key = $this->getCacheKey();

        if (!\array_key_exists($key, AbstractEagerCache::$caches)) {
            AbstractEagerCache::$caches[$key] = CacheHandler::getInstance()->get(
                $key,
                $this,
            );
        }

        return AbstractEagerCache::$caches[$key];
    }

    private function getCacheKey(): string
    {
        if (!isset($this->cacheName)) {
            /* @see AbstractCacheBuilder::getCacheName() */
            $reflection = new \ReflectionClass($this);
            $this->cacheName = \str_replace(
                ['\\', 'system_cache_eager_'],
                ['_', ''],
                \get_class($this)
            );

            $parameters = ClassUtil::getObjectProperties($this, \ReflectionProperty::IS_READONLY);

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
        CacheHandler::getInstance()->get($this->getCacheKey(), $this, true);
    }
}
