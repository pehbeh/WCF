<?php

namespace wcf\system\cache\tolerant;

use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\TolerantCacheRebuildBackgroundJob;
use wcf\system\cache\CacheHandler;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @template T of array|object
 */
abstract class AbstractTolerantCache
{
    /**
     * @var T
     */
    private array|object $cache;
    private string $cacheName;

    /**
     * @return T
     */
    final public function getCache(): array|object
    {
        if (!isset($this->cache)) {
            $cache = CacheHandler::getInstance()->getCacheSource()->get(
                $this->getCacheKey(),
                0,
            );

            if ($cache === null) {
                $this->rebuild();
            } else {
                $this->cache = $cache;
            }

            if ($this->needsRebuild()) {
                BackgroundQueueHandler::getInstance()->enqueueIn(
                    [new TolerantCacheRebuildBackgroundJob(\get_class($this), $this->getProperties())]
                );
            }
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

    final public function rebuild(): void
    {
        $newCacheData = $this->rebuildCacheData();

        if (!isset($this->cache)) {
            $this->cache = $newCacheData;
        }

        CacheHandler::getInstance()->getCacheSource()->set(
            $this->getCacheKey(),
            $newCacheData,
            0
        );
    }

    /**
     * @return T
     */
    abstract protected function rebuildCacheData(): array|object;

    final public function nextRebuildTime(): int
    {
        $cacheTime = CacheHandler::getInstance()->getCacheSource()->getCreationTime(
            $this->getCacheKey(),
            $this->getLifetime()
        );

        if ($cacheTime === null) {
            return \TIME_NOW;
        }

        return $cacheTime + ($this->getLifetime() - 60);
    }

    /**
     * Return the lifetime of the cache in seconds.
     */
    abstract public function getLifetime(): int;

    final public function needsRebuild(): bool
    {
        return TIME_NOW >= $this->nextRebuildTime();
    }
}
