<?php

namespace wcf\system\user\command;

use wcf\data\file\File;
use wcf\data\file\FileAction;
use wcf\data\user\avatar\UserAvatarAction;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\user\storage\UserStorageHandler;

final class SetAvatar
{
    public function __construct(
        private readonly User $user,
        private readonly ?File $file = null
    ) {
    }

    public function __invoke()
    {
        if ($this->file === null) {
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
    }
}
