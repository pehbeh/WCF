<?php

namespace wcf\event\user\profile;

use wcf\data\user\UserProfile;
use wcf\event\IPsr14Event;
use wcf\system\view\user\profile\UserProfileHeaderViewStatItem;

/**
 * Collects the statistic items for the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderStatItemCollecting implements IPsr14Event
{
    /**
     * @var UserProfileHeaderViewStatItem[]
     */
    private array $items = [];

    public function __construct(public readonly UserProfile $user) {}

    public function register(UserProfileHeaderViewStatItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return UserProfileHeaderViewStatItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
