<?php

namespace wcf\data\user\notification;

use wcf\data\DatabaseObjectEditor;
use wcf\system\user\notification\UserNotificationHandler;

/**
 * Provides functions to edit user notifications.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       UserNotification
 * @extends DatabaseObjectEditor<UserNotification>
 */
class UserNotificationEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserNotification::class;

    /**
     * Marks this notification as confirmed.
     *
     * @return void
     * @deprecated 5.2 Please use `UserNotificationHandler::markAsConfirmedByIDs()` instead.
     */
    public function markAsConfirmed()
    {
        UserNotificationHandler::getInstance()->markAsConfirmedByIDs([$this->notificationID]);
    }
}
