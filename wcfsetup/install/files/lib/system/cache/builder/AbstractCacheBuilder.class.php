<?php

namespace wcf\system\cache\builder;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;

/**
 * Default implementation for cache builders.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractCacheBuilder extends SingletonFactory implements ICacheBuilder
{
    /**
     * list of cache resources by index
     * @var mixed[][]
     */
    protected $cache = [];

    /**
     * maximum cache lifetime in seconds, '0' equals infinite
     * @var int
     */
    protected $maxLifetime = 0;

    /**
     * @inheritDoc
     */
    public function getData(array $parameters = [], $arrayIndex = '')
    {
        $index = CacheHandler::getInstance()->getCacheIndex($parameters);

        if (!isset($this->cache[$index])) {
            // fetch cache or rebuild if missing
            $this->cache[$index] = CacheHandler::getInstance()->getCacheAdapter()->get(
                $this->getCacheName($parameters),
                function (ItemInterface $item) use ($parameters) {
                    if ($this->getMaxLifetime() > 0) {
                        $item->expiresAfter($this->getMaxLifetime());
                    }

                    return $this->rebuild($parameters);
                }
            );
        }

        if (!empty($arrayIndex)) {
            if (!\array_key_exists($arrayIndex, $this->cache[$index])) {
                throw new SystemException("array index '" . $arrayIndex . "' does not exist in cache resource");
            }

            return $this->cache[$index][$arrayIndex];
        }

        return $this->cache[$index];
    }

    /**
     * @inheritDoc
     */
    public function getMaxLifetime()
    {
        return $this->maxLifetime;
    }

    /**
     * @inheritDoc
     */
    public function reset(array $parameters = [])
    {
        CacheHandler::getInstance()->delete($this->getCacheName($parameters));
    }

    /**
     * Rebuilds cache for current resource.
     *
     * @param array $parameters
     */
    abstract protected function rebuild(array $parameters);

    /**
     * Builds cache name.
     */
    protected function getCacheName(array $parameters = []): string
    {
        $cacheName = \str_replace(
            ['\\', 'system_cache_builder_'],
            ['_', ''],
            \get_class($this)
        );
        if (!empty($parameters)) {
            $cacheName .= '-' . CacheHandler::getInstance()->getCacheIndex($parameters);
        }

        return $cacheName;
    }
}
