<?php

namespace wcf\system\view\user\profile;

/**
 * Represents an interaction option in the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderViewInteractionOption
{
    public static function forLink(
        string $title,
        string $link,
        string $attributes = ''
    ): self {
        return new self($title, $link, $attributes);
    }

    public static function forButton(
        string $title,
        string $attributes = ''
    ): self {
        return new self($title, '', $attributes);
    }

    private function __construct(
        public readonly string $title,
        public readonly string $link = '',
        public readonly string $attributes = '',
    ) {}
}
