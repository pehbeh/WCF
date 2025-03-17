<?php

namespace wcf\system\cache\ephemeral;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\EphemeralCacheRebuildBackgroundJob;
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
abstract class AbstractEphemeralCache implements ICacheCallback
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
            $this->cache = CacheHandler::getInstance()->getCacheAdapter()->get(
                $this->getCacheKey(),
                function (ItemInterface $item, bool &$save) {
                    if (!$item->isHit()) {
                        return ($this)($item);
                    }

                    BackgroundQueueHandler::getInstance()->enqueueIn(
                        new EphemeralCacheRebuildBackgroundJob(
                            $item,
                            \get_class($this),
                            ClassUtil::getObjectProperties($this, \ReflectionProperty::IS_READONLY)
                        )
                    );

                    $save = false;

                    return $item->get();
                },
                5500.0
            );
        }

        return $this->cache;
    }

    private function getCacheKey(): string
    {
        if (!isset($this->cacheName)) {
            /* @see AbstractEagerCache::getCacheKey() */
            $this->cacheName = \str_replace(
                ['\\', 'system_cache_ephemeral_'],
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
}
