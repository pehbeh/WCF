<?php

namespace wcf\system\file\processor;

use wcf\data\file\File;
use wcf\data\file\thumbnail\FileThumbnail;
use wcf\data\user\avatar\UserAvatar;
use wcf\data\user\User;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\WCF;

/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserAvatarFileProcessor extends AbstractFileProcessor
{
    #[\Override]
    public function getObjectTypeName(): string
    {
        return 'com.woltlab.wcf.user.avatar';
    }

    #[\Override]
    public function getAllowedFileExtensions(array $context): array
    {
        return \explode("\n", WCF::getSession()->getPermission('user.profile.avatar.allowedFileExtensions'));
    }

    #[\Override]
    public function canAdopt(File $file, array $context): bool
    {
        // TODO
        return true;
    }

    #[\Override]
    public function adopt(File $file, array $context): void
    {
        // TODO
    }

    #[\Override]
    public function acceptUpload(string $filename, int $fileSize, array $context): FileProcessorPreflightResult
    {
        // TODO
        return FileProcessorPreflightResult::Passed;
    }

    #[\Override]
    public function canDelete(File $file): bool
    {
        // TODO
        return true;
    }

    #[\Override]
    public function canDownload(File $file): bool
    {
        // TODO
        return true;
    }

    #[\Override]
    public function getMaximumCount(array $context): ?int
    {
        // TODO
        return null;
    }

    #[\Override]
    public function getThumbnailFormats(): array
    {
        return [
            // TODO did we need thumbnails for sizes less then 128x128?
            // 96x96
            // 64x64
            // 48x48
            // 32x32
            new ThumbnailFormat('128', UserAvatar::AVATAR_SIZE, UserAvatar::AVATAR_SIZE, false),
            new ThumbnailFormat('256', UserAvatar::AVATAR_SIZE_2X, UserAvatar::AVATAR_SIZE_2X, false),
        ];
    }

    #[\Override]
    public function adoptThumbnail(FileThumbnail $thumbnail): void
    {
        // TODO
    }

    #[\Override]
    public function delete(array $fileIDs, array $thumbnailIDs): void
    {
        // TODO
    }

    #[\Override]
    public function countExistingFiles(array $context): ?int
    {
        // TODO
    }

    #[\Override]
    public function getMaximumSize(array $context): ?int
    {
        /**
         * Reject the file if it is larger than 750 kB after resizing. A worst-case
         * completely-random 128x128 PNG is around 35 kB and JPEG is around 50 kB.
         *
         * Animated GIFs can be much larger depending on the length of animation,
         * 750 kB seems to be a reasonable upper bound for anything that can be
         * considered reasonable with regard to "distraction" and mobile data
         * volume.
         */
        return 750_000;
    }

    #[\Override]
    public function getImageCropperConfiguration(): ?ImageCropperConfiguration
    {
        return ImageCropperConfiguration::createExact(
            new ImageCropSize(UserAvatar::AVATAR_SIZE, UserAvatar::AVATAR_SIZE),
            new ImageCropSize(UserAvatar::AVATAR_SIZE_2X, UserAvatar::AVATAR_SIZE_2X)
        );
    }

    private function getUser(array $context): ?User
    {
        $userID = $context['objectID'] ?? null;
        if ($userID === null) {
            return null;
        }

        return UserRuntimeCache::getInstance()->getObject($userID);
    }
}
