<?php

namespace wcf\system\cache\tolerant;

use wcf\data\user\UserProfile;
use wcf\data\user\UserProfileList;
use wcf\system\cache\tolerant\data\UserStatsCacheData;
use wcf\system\WCF;

/**
 * Caches the number of members and the newest member.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractTolerantCache<UserStatsCacheData>
 */
final class UserStatsCache extends AbstractTolerantCache
{
    #[\Override]
    public function getLifetime(): int
    {
        return 600;
    }

    #[\Override]
    protected function rebuildCacheData(): UserStatsCacheData
    {
        return new UserStatsCacheData(
            $this->getMembers(),
            $this->getNewestMember()
        );
    }

    private function getMembers(): int
    {
        $sql = "SELECT COUNT(*)
                FROM   wcf1_user";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();

        return $statement->fetchSingleColumn();
    }

    private function getNewestMember(): UserProfile
    {
        $userProfileList = new UserProfileList();
        $userProfileList->sqlLimit = 1;
        $userProfileList->sqlOrderBy = 'user_table.userID DESC';
        $userProfileList->readObjects();

        return $userProfileList->getSingleObject();
    }
}
