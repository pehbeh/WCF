<?php

namespace wcf\system\file\processor;

/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ImageCropSize implements \JsonSerializable
{
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {
        if ($width <= 0 || $height <= 0) {
            throw new \OutOfRangeException("The width and height values must be larger than 0.");
        }
    }

    public function aspectRatio(): float
    {
        return $this->width / $this->height;
    }

    #[\Override]
    public function jsonSerialize(): mixed
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
