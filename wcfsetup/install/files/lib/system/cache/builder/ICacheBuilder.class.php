<?php

namespace wcf\system\cache\builder;

/**
 * A cache builder provides data for the cache handler that ought to be cached.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ICacheBuilder
{
    /**
     * Returns the data that ought to be cached.
     *
     * @param mixed[] $parameters
     * @param string $arrayIndex
     * @return mixed
     */
    public function getData(array $parameters = [], $arrayIndex = '');

    /**
     * Returns maximum lifetime for cache resource.
     *
     * @return int
     */
    public function getMaxLifetime();

    /**
     * Flushes cache. If no parameters are given, all caches starting with
     * the same cache name will be flushed too.
     *
     * @param mixed[] $parameters
     * @return void
     */
    public function reset(array $parameters = []);
}
