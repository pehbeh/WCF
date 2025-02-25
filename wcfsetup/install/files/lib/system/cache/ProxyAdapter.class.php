<?php

namespace wcf\system\cache;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class ProxyAdapter implements CacheInterface
{
    public function __construct(private readonly CacheInterface $pool)
    {
    }

    #[\Override]
    public function get(
        string $key,
        callable $callback,
        ?float $beta = null,
        ?array &$metadata = null
    ): mixed {
        return $this->pool->get(
            $key,
            static function (ItemInterface $item, bool &$save) use ($callback) {
                if (!$item->isHit()) {
                    return $callback($item, $save);
                }

                $save = false;

                $metadata = $item->getMetadata();
                $expiry = $metadata[ItemInterface::METADATA_EXPIRY] ?? 0;

                // TODO add background job to refresh cache

                return $item->get();
            },
            $beta,
            $metadata
        );
    }

    #[\Override]
    public function delete(string $key): bool
    {
        return $this->pool->delete($key);
    }
}
