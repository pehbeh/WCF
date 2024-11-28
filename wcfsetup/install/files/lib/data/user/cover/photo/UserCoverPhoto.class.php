<?php

namespace wcf\data\user\cover\photo;

use wcf\data\file\File;

/**
 * Represents a user's cover photo.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class UserCoverPhoto implements IUserCoverPhoto
{
    public const MAX_HEIGHT = 800;

    public const MAX_WIDTH = 2000;

    public const MIN_HEIGHT = 200;

    public const MIN_WIDTH = 500;

    /**
     * UserCoverPhoto constructor.
     */
    public function __construct(
        protected readonly int $userID,
        protected readonly File $file
    ) {
    }

    #[\Override]
    public function getLocation(?bool $forceWebP = null): string
    {
        return $this->file->getPath();
    }

    #[\Override]
    public function getURL(?bool $forceWebP = null): string
    {
        return $this->file->getLink();
    }

    #[\Override]
    public function getFilename(?bool $forceWebP = null): string
    {
        return $this->file->filename;
    }

    /**
     * Returns the minimum and maximum dimensions for cover photos.
     */
    public static function getCoverPhotoDimensions(): array
    {
        return [
            'max' => [
                'height' => self::MAX_HEIGHT,
                'width' => self::MAX_WIDTH,
            ],
            'min' => [
                'height' => self::MIN_HEIGHT,
                'width' => self::MIN_WIDTH,
            ],
        ];
    }
}
