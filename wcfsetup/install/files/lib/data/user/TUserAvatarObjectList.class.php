<?php

namespace wcf\data\user;

use wcf\data\user\UserProfileList;
use wcf\system\cache\runtime\FileRuntimeCache;

/**
 * Contains methods to load avatar files for a list of `UserProfile`.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    UserProfile[] $objects
 * @mixin UserProfileList
 *
 * @since 6.2
 */
trait TUserAvatarObjectList
{
    protected function cacheAvatarFiles(): void
    {
        $avatarFileIDs = [];
        foreach ($this->objects as $user) {
            if ($user->avatarFileID !== null) {
                $avatarFileIDs[] = $user->avatarFileID;
            }
        }
        if ($avatarFileIDs === []) {
            return;
        }

        FileRuntimeCache::getInstance()->cacheObjectIDs($avatarFileIDs);
    }
}
