<?php

namespace wcf\system\cache\eager;

use wcf\system\cache\CacheHandler;

/**
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractEagerCache implements IEagerCache
{
    private static array $caches = [];
    private string $cacheName;

    #[\Override]
    public function getCache(): array | object
    {
        $key = $this->getCacheKey();

        if (!\array_key_exists($key, AbstractEagerCache::$caches)) {
            AbstractEagerCache::$caches[$key] = CacheHandler::getInstance()->getCacheSource()->get($key, 0);
            if (AbstractEagerCache::$caches[$key] === null) {
                $this->reset();
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

    #[\Override]
    public function reset(): void
    {
        $key = $this->getCacheKey();
        AbstractEagerCache::$caches[$key] = $this->rebuild();
        CacheHandler::getInstance()->getCacheSource()->set($key, AbstractEagerCache::$caches[$key], 0);
    }

    abstract protected function rebuild(): mixed;
}
