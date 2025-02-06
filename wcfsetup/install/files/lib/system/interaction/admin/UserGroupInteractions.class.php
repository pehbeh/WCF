<?php

namespace wcf\system\interaction\admin;

use wcf\data\user\group\UserGroup;
use wcf\event\interaction\admin\UserGroupInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for user groups.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserGroupInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction("core/users/groups/%s", static fn(UserGroup $group) => $group->isDeletable())
        ]);

        EventHandler::getInstance()->fire(
            new UserGroupInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return UserGroup::class;
    }
}
