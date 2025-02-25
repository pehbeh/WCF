<?php

namespace wcf\system\cache\tolerant;

use Symfony\Contracts\Cache\CacheInterface;
use wcf\system\cache\CacheHandler;
use wcf\system\cache\ICacheCallback;
use wcf\system\cache\ProxyAdapter;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @template T of array|object
 * @implements ICacheCallback<T>
 */
abstract class AbstractTolerantCache implements ICacheCallback
{
    /**
     * @var T
     */
    private array|object $cache;
    private string $cacheName;

    /**
     * @return T
     */
    final public function get(): array|object
    {
        if (!isset($this->cache)) {
            $this->cache = AbstractTolerantCache::getCacheAdapter()->get(
                $this->getCacheKey(),
                $this,
            );
        }

        return $this->cache;
    }

    private function getCacheKey(): string
    {
        if (!isset($this->cacheName)) {
            /* @see AbstractEagerCache::getCacheKey() */
            $this->cacheName = \str_replace(
                ['\\', 'system_cache_tolerant_'],
                ['_', ''],
                \get_class($this)
            );

            $parameters = $this->getProperties();

            if ($parameters !== []) {
                $this->cacheName .= '-' . CacheHandler::getInstance()->getCacheIndex($parameters);
            }
        }

        return $this->cacheName;
    }

    private static function getCacheAdapter(): CacheInterface
    {
        static $cacheAdapter;
        if (!isset($cacheAdapter)) {
            $cacheAdapter = new ProxyAdapter(CacheHandler::getInstance()->getCacheAdapter());
        }

        return $cacheAdapter;
    }

    /**
     * @return array<string, mixed>
     */
    private function getProperties(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = [];
        foreach ($reflection->getProperties(\ReflectionProperty::IS_READONLY) as $property) {
            if (!$property->isInitialized($this)) {
                continue;
            }

            if ($property->getValue($this) === null) {
                continue;
            }

            $properties[$property->getName()] = $property->getValue($this);
        }

        return $properties;
    }
}
