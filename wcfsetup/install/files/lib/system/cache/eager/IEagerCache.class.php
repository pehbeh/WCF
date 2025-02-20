<?php

namespace wcf\system\cache\eager;

/**
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
interface IEagerCache
{
    /**
     * Returns the cache.
     */
    public function getCache(): array | object;

    /**
     * Rebuilds the cache and stores it in the cache source.
     * This method MUST NOT rely on any (runtime) cache at any point because those could be stale.
     */
    public function reset(): void;
}
