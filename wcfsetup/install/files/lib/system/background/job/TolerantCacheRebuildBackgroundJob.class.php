<?php

namespace wcf\system\background\job;

use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\ItemInterface;
use wcf\system\cache\CacheHandler;
use wcf\system\cache\tolerant\AbstractTolerantCache;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class TolerantCacheRebuildBackgroundJob extends AbstractUniqueBackgroundJob
{
    private readonly ItemInterface $item;
    public function __construct(
        /** @var ItemInterface $item */
        ItemInterface $item,
        /** @var class-string<AbstractTolerantCache<array|object> */
        public readonly string $cacheClass,
        /** @var array<string, mixed> */
        public readonly array $parameters = [],
    ) {
        $this->item = clone $item;
        $this->item->set(null);
    }

    public function identifier(): string
    {
        $identifier = $this->cacheClass;
        if (!empty($this->parameters)) {
            $identifier .= '-' . CacheHandler::getInstance()->getCacheIndex($this->parameters);
        }

        return $identifier;
    }

    #[\Override]
    public function newInstance(): static
    {
        return new TolerantCacheRebuildBackgroundJob($this->item, $this->cacheClass, $this->parameters);
    }

    #[\Override]
    public function queueAgain(): bool
    {
        return false;
    }

    #[\Override]
    public function perform()
    {
        if (!\class_exists($this->cacheClass)) {
            return;
        }

        // @see https://github.com/symfony/symfony/blob/7.2/src/Symfony/Component/Cache/Messenger/EarlyExpirationHandler.php

        $startTime = microtime(true);

        $tolerantCache = new $this->cacheClass(...$this->parameters);

        $save = true;
        $value = ($tolerantCache)($this->item, $save);

        static $setMetadata;

        $setMetadata ??= \Closure::bind(
            function (CacheItem $item, float $startTime) {
                if ($item->expiry > $endTime = microtime(true)) {
                    $item->newMetadata[ItemInterface::METADATA_EXPIRY] = $item->expiry;
                    $item->newMetadata[ItemInterface::METADATA_CTIME] = (int)ceil(1000 * ($endTime - $startTime));
                }
            },
            null,
            CacheItem::class
        );

        $setMetadata($this->item, $startTime);

        CacheHandler::getInstance()->getCacheAdapter()->save($this->item->set($value));
    }
}
