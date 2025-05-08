<?php

namespace wcf\system\cache\builder;

use wcf\data\user\rank\UserRank;
use wcf\system\cache\eager\UserRankCache;

/**
 * Caches the list of user ranks.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 * @deprecated 6.2 use `UserRankCache` instead
 */
final class UserRankCacheBuilder extends AbstractLegacyCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        return (new UserRankCache())->getCache();
    }

    public function getRank(int $rankID): ?UserRank
    {
        return (new UserRankCache())->getRank($rankID);
    }

    #[\Override]
    public function reset(array $parameters = [])
    {
        (new UserRankCache())->rebuild();
    }
}
