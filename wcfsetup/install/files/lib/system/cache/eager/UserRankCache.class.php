<?php

namespace wcf\system\cache\eager;

use wcf\data\user\rank\ViewableUserRank;
use wcf\data\user\rank\ViewableUserRankList;

/**
 * Eager cache implementation for user ranks.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractEagerCache<array<int, ViewableUserRank>>
 */
final class UserRankCache extends AbstractEagerCache
{
    public function __construct(
        public readonly int $languageID
    ) {
    }

    #[\Override]
    protected function getCacheData(): array
    {
        $userRankList = new ViewableUserRankList($this->languageID);
        $userRankList->readObjects();

        return $userRankList->getObjects();
    }
}
