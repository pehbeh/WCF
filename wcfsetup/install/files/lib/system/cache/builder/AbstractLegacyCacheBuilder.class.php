<?php

namespace wcf\system\cache\builder;

use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;

/**
 * Legacy implementation of the ICacheBuilder interface that has been migrated to a new EagerCache or AsyncCache.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @since       6.2
 * @deprecated  6.2
 */
abstract class AbstractLegacyCacheBuilder extends SingletonFactory implements ICacheBuilder
{
    #[\Override]
    public function getData(array $parameters = [], $arrayIndex = '')
    {
        $cache = $this->rebuild($parameters);

        if (!empty($arrayIndex)) {
            if (!\array_key_exists($arrayIndex, $cache)) {
                throw new SystemException("array index '" . $arrayIndex . "' does not exist in cache resource");
            }

            return $cache[$arrayIndex];
        }

        return $cache;
    }

    /**
     * Rebuilds cache for current resource.
     */
    abstract protected function rebuild(array $parameters): array;

    #[\Override]
    final public function getMaxLifetime()
    {
        return 0;
    }
}
