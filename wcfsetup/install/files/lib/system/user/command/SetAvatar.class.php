<?php

namespace wcf\system\user\command;

use wcf\data\file\File;
use wcf\data\file\FileAction;
use wcf\data\user\avatar\UserAvatarAction;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\user\group\assignment\UserGroupAssignmentHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\user\UserProfileHandler;
use wcf\system\WCF;

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

        // Delete old `UserAvatar` object if provided
        if ($this->user->avatarID) {
            (new UserAvatarAction([$this->user->avatarID], 'delete'))->executeAction();
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
