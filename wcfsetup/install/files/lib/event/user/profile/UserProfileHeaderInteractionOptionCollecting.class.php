<?php

namespace wcf\event\user\profile;

use wcf\data\user\UserProfile;
use wcf\event\IPsr14Event;
use wcf\system\view\user\profile\UserProfileHeaderViewInteractionOption;

/**
 * Collects the interaction options for the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderInteractionOptionCollecting implements IPsr14Event
{
    /**
     * @var UserProfileHeaderViewInteractionOption[]
     */
    private array $options = [];

    public function __construct(public readonly UserProfile $user) {}

    public function register(UserProfileHeaderViewInteractionOption $option): void
    {
        $this->options[] = $option;
    }

    /**
     * @return UserProfileHeaderViewInteractionOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
