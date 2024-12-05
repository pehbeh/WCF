<?php

namespace wcf\event\user\profile;

use wcf\data\user\UserProfile;
use wcf\event\IPsr14Event;
use wcf\system\view\user\profile\UserProfileHeaderViewManagementOption;

/**
 * Collects the management options for the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderManagementOptionCollecting implements IPsr14Event
{
    /**
     * @var UserProfileHeaderViewManagementOption[]
     */
    private array $options = [];

    public function __construct(public readonly UserProfile $user) {}

    public function register(UserProfileHeaderViewManagementOption $option): void
    {
        $this->options[] = $option;
    }

    /**
     * @return UserProfileHeaderViewManagementOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
