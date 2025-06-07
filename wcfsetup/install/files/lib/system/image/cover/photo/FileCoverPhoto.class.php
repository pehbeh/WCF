<?php

namespace wcf\system\image\cover\photo;

use wcf\data\file\File;

/**
 * Represents a cover photo that is based on a file upload.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class FileCoverPhoto implements ICoverPhoto
{
    public function __construct(private readonly File $file) {}

    #[\Override]
    public function getUrl(?string $size = null): string
    {
        if ($size !== null) {
            $thumbnail = $this->file->getThumbnail($size);
            if ($thumbnail !== null) {
                return $thumbnail->getLink();
            }
        }

        return $this->file->getFullSizeImageSource() ?: $this->file->getLink();
    }

    #[\Override]
    public function getWidth(?string $size = null): int
    {
        if ($size !== null) {
            $thumbnail = $this->file->getThumbnail($size);
            if ($thumbnail !== null) {
                return $thumbnail->width;
            }
        }

        return $this->file->width;
    }

    #[\Override]
    public function getHeight(?string $size = null): int
    {
        if ($size !== null) {
            $thumbnail = $this->file->getThumbnail($size);
            if ($thumbnail !== null) {
                return $thumbnail->height;
            }
        }

        return $this->file->height;
    }
}
