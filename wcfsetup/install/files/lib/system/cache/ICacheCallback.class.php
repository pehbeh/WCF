<?php

namespace wcf\system\cache;

use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @template T of array|object
 */
interface ICacheCallback
{
    /**
     * Generates the cache data and returns it.
     * This method MUST NOT rely on any (runtime) cache at any point because those could be stale.
     *
     * @return T
     */
    public function __invoke(ItemInterface $item): array|object;
}
