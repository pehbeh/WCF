<?php

namespace wcf\system\user\command;

use wcf\data\file\File;
use wcf\data\file\FileAction;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\cache\runtime\UserProfileRuntimeCache;

/**
 * Sets the cover photo of a user.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class SetCoverPhoto
{
    public function __construct(
        private readonly User $user,
        private readonly ?File $file = null
    ) {
    }

    public function __invoke(): void
    {
        if ($this->file === null && $this->user->coverPhotoFileID !== null) {
            (new FileAction([$this->user->coverPhotoFileID], 'delete'))->executeAction();
        }

        // Delete the old cover photo if it exists.
        $oldCoverPhotoLocation = self::getCoverPhotoLocation($this->user, false);
        $oldCoverPhotoWebPLocation = self::getCoverPhotoLocation($this->user, true);

        if ($oldCoverPhotoLocation && \file_exists($oldCoverPhotoLocation)) {
            @\unlink($oldCoverPhotoLocation);
        }
        if ($oldCoverPhotoWebPLocation && \file_exists($oldCoverPhotoWebPLocation)) {
            @\unlink($oldCoverPhotoWebPLocation);
        }

        (new UserEditor($this->user))->update([
            'coverPhotoFileID' => $this->file?->fileID,
            'coverPhotoHash' => null,
            'coverPhotoExtension' => '',
            'coverPhotoHasWebP' => 0,
        ]);
        UserProfileRuntimeCache::getInstance()->removeObject($this->user->userID);
    }

    /**
     * Returns the location of a user's cover photo before WCF6.2.
     */
    /** @noinspection PhpUndefinedFieldInspection */
    public static function getCoverPhotoLocation(User $user, bool $forceWebP): ?string
    {
        if (!$user->coverPhotoHash || !$user->coverPhotoExtension) {
            return null;
        }

        return \sprintf(
            '%simages/coverPhotos/%s/%d-%s.%s',
            WCF_DIR,
            \substr(
                $user->coverPhotoHash,
                0,
                2
            ),
            $user->userID,
            $user->coverPhotoHash,
            $forceWebP ? 'webp' : $user->coverPhotoExtension
        );
    }
}
