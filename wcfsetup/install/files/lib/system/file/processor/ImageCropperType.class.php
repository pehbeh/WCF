<?php

namespace wcf\system\file\processor;

/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
enum ImageCropperType
{
    case MinMax;
    case Exact;

    public function toString(): string
    {
        return match ($this) {
            self::MinMax => 'minMax',
            self::Exact => 'exact',
        };
    }

    public static function fromString(string $fileType): self
    {
        return match ($fileType) {
            'minMax' => self::MinMax,
            'exact' => self::Exact,
        };
    }
}
