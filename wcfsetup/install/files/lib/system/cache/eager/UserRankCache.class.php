<?php

namespace wcf\system\cache\eager;

use wcf\data\user\rank\UserRank;
use wcf\data\user\rank\UserRankList;

/**
 * Eager cache implementation for user ranks.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractEagerCache<array<int, UserRank>>
 */
final class UserRankCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): array
    {
        $userRankList = new UserRankList();
        $userRankList->readObjects();

        return $userRankList->getObjects();
    }
}
