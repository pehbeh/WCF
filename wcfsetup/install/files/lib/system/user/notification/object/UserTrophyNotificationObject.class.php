<?php

namespace wcf\system\user\notification\object;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\trophy\UserTrophy;

/**
 * Represents a user trophy notification object.
 *
 * @author  Joshua Ruesweg
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin   UserTrophy
 * @extends DatabaseObjectDecorator<UserTrophy>
 */
class UserTrophyNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserTrophy::class;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTrophy()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getURL()
    {
        return $this->getDecoratedObject()->getTrophy()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getAuthorID()
    {
        return $this->getDecoratedObject()->userID;
    }
}
