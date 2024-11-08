<?php

namespace wcf\system\user\command;

use wcf\data\file\File;
use wcf\data\file\FileAction;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\user\group\assignment\UserGroupAssignmentHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\user\UserProfileHandler;
use wcf\system\WCF;

/**
 * Sets the avatar of a user.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class SetAvatar
{
    public function __construct(
        private readonly User $user,
        private readonly ?File $file = null
    ) {
    }

    public function __invoke()
    {
        if ($this->file === null && $this->user->avatarFileID !== null) {
            (new FileAction([$this->user->avatarFileID], 'delete'))->executeAction();
        }

        (new UserEditor($this->user))->update([
            'avatarFileID' => $this->file?->fileID,
            'avatarID' => null,
        ]);

        UserStorageHandler::getInstance()->reset([$this->user->userID], 'avatar');

        // check if the user will be automatically added to new user groups
        // because of the changed avatar
        UserGroupAssignmentHandler::getInstance()->checkUsers([$this->user->userID]);

        if ($this->user->userID === WCF::getUser()->userID) {
            UserProfileHandler::getInstance()->reloadUserProfile();
        }
    }
}
