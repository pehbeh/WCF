<?php

namespace wcf\data\user;

use wcf\data\DatabaseObjectList;
use wcf\data\file\FileList;

/**
 * Contains methods to load avatar files for a list of `UserProfile`.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    UserProfile[] $objects
 * @mixin DatabaseObjectList
 */
trait TUserAvatarObjectList
{
    public bool $loadAvatarFiles = true;

    protected function loadAvatarFiles(): void
    {
        if (!$this->loadAvatarFiles) {
            return;
        }

        $avatarFileIDs = [];
        foreach ($this->objects as $user) {
            if ($user->avatarFileID !== null) {
                $avatarFileIDs[] = $user->avatarFileID;
            }
        }
        if ($avatarFileIDs === []) {
            return;
        }

        $fileList = new FileList();
        $fileList->loadThumbnails = true;
        $fileList->setObjectIDs($avatarFileIDs);
        $fileList->readObjects();
        $files = $fileList->getObjects();

        foreach ($this->objects as $user) {
            if ($user->avatarFileID !== null) {
                $user->setFileAvatar($files[$user->avatarFileID]);
            }
        }
    }
}
