<?php

namespace wcf\system\view\user\profile;

/**
 * Represents an item of the statistics in the user profile.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileStatItem
{
    public static function forText(string $title, string $value): self
    {
        return new self($title, $value);
    }

    public static function forLink(
        string $title,
        string $value,
        string $link,
        string $cssClassName = '',
        string $attributes = ''
    ): self {
        return new self($title, $value, $cssClassName, $link, false, $attributes);
    }

    public static function forButton(
        string $title,
        string $value,
        string $cssClassName = '',
        string $attributes = ''
    ): self {
        return new self($title, $value, $cssClassName, '', true, $attributes);
    }

    private function __construct(
        public readonly string $title,
        public readonly string $value,
        public readonly string $cssClassName = '',
        public readonly string $link = '',
        public readonly bool $isButton = false,
        public readonly string $attributes = '',
    ) {}
}
