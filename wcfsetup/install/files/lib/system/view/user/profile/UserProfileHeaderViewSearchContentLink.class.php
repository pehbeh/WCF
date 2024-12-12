<?php

namespace wcf\system\view\user\profile;

/**
 * Represents a link in the search content dropdown in the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderViewSearchContentLink
{
    public function __construct(
        public readonly string $title,
        public readonly string $link = ''
    ) {}
}
