<?php

namespace wcf\system\cache\tolerant\data;

use wcf\data\user\UserProfile;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class UserStatsCacheData
{
    public function __construct(
        public readonly int $members,
        public readonly UserProfile $newestMember
    ) {
    }
}
