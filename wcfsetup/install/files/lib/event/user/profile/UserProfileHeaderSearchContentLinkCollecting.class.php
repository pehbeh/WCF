<?php

namespace wcf\event\user\profile;

use wcf\data\user\UserProfile;
use wcf\event\IPsr14Event;
use wcf\system\view\user\profile\UserProfileHeaderViewSearchContentLink;

/**
 * Collects the search content links for the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderSearchContentLinkCollecting implements IPsr14Event
{
    /**
     * @var UserProfileHeaderViewSearchContentLink[]
     */
    private array $links = [];

    public function __construct(public readonly UserProfile $user) {}

    public function register(UserProfileHeaderViewSearchContentLink $link): void
    {
        $this->links[] = $link;
    }

    /**
     * @return UserProfileHeaderViewSearchContentLink[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
