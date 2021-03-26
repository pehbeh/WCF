<?php

namespace wcf\data\user\notification;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\IUserNotificationEvent;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Executes user notification-related actions.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\Data\User\Notification
 *
 * @method  UserNotificationEditor[]    getObjects()
 * @method  UserNotificationEditor      getSingleObject()
 */
class UserNotificationAction extends AbstractDatabaseObjectAction
{
    /**
     * notification editor object
     * @var UserNotificationEditor
     */
    public $notificationEditor;

    /**
     * @inheritDoc
     * @return  UserNotification
     */
    public function create()
    {
        /** @var UserNotification $notification */
        $notification = parent::create();

        $sql = "INSERT INTO wcf" . WCF_N . "_user_notification_to_user
                            (notificationID, userID)
                VALUES      (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $notification->notificationID,
            $notification->userID,
        ]);

        return $notification;
    }

    /**
     * Creates a simple notification without stacking support, applies to legacy notifications too.
     *
     * @return  mixed[][]
     */
    public function createDefault()
    {
        $notifications = [];
        foreach ($this->parameters['recipients'] as $recipient) {
            $this->parameters['data']['userID'] = $recipient->userID;
            $this->parameters['data']['mailNotified'] = (($recipient->mailNotificationType == 'none' || $recipient->mailNotificationType == 'instant') ? 1 : 0);
            $notification = $this->create();

            $notifications[$recipient->userID] = [
                'isNew' => true,
                'object' => $notification,
            ];
        }

        // insert author
        $sql = "INSERT INTO wcf" . WCF_N . "_user_notification_author
                            (notificationID, authorID, time)
                VALUES      (?, ?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($notifications as $notificationData) {
            $statement->execute([
                $notificationData['object']->notificationID,
                $this->parameters['authorID'] ?: null,
                TIME_NOW,
            ]);
        }
        WCF::getDB()->commitTransaction();

        return $notifications;
    }

    /**
     * Creates a notification or adds another author to an existing one.
     *
     * @return  mixed[][]
     */
    public function createStackable()
    {
        // get existing notifications
        $notificationList = new UserNotificationList();
        $notificationList->getConditionBuilder()->add("eventID = ?", [$this->parameters['data']['eventID']]);
        $notificationList->getConditionBuilder()->add("eventHash = ?", [$this->parameters['data']['eventHash']]);
        $notificationList->getConditionBuilder()->add("userID IN (?)", [\array_keys($this->parameters['recipients'])]);
        $notificationList->getConditionBuilder()->add("confirmTime = ?", [0]);
        $notificationList->readObjects();
        $existingNotifications = [];
        foreach ($notificationList as $notification) {
            $existingNotifications[$notification->userID] = $notification;
        }

        $notifications = [];
        foreach ($this->parameters['recipients'] as $recipient) {
            $notification = ($existingNotifications[$recipient->userID] ?? null);
            $isNew = ($notification === null);

            if ($notification === null) {
                $this->parameters['data']['userID'] = $recipient->userID;
                $this->parameters['data']['mailNotified'] = (($recipient->mailNotificationType == 'none' || $recipient->mailNotificationType == 'instant') ? 1 : 0);
                $notification = $this->create();
            }

            $notifications[$recipient->userID] = [
                'isNew' => $isNew,
                'object' => $notification,
            ];
        }

        \uasort($notifications, static function ($a, $b) {
            if ($a['object']->notificationID == $b['object']->notificationID) {
                return 0;
            } elseif ($a['object']->notificationID < $b['object']->notificationID) {
                return -1;
            }

            return 1;
        });

        // insert author
        $sql = "INSERT IGNORE INTO  wcf" . WCF_N . "_user_notification_author
                                    (notificationID, authorID, time)
                VALUES              (?, ?, ?)";
        $authorStatement = WCF::getDB()->prepareStatement($sql);

        // update trigger count
        $sql = "UPDATE  wcf" . WCF_N . "_user_notification
                SET     timesTriggered = timesTriggered + ?,
                        guestTimesTriggered = guestTimesTriggered + ?
                WHERE   notificationID = ?";
        $triggerStatement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        $notificationIDs = [];
        foreach ($notifications as $notificationData) {
            $notificationIDs[] = $notificationData['object']->notificationID;

            $authorStatement->execute([
                $notificationData['object']->notificationID,
                $this->parameters['authorID'] ?: null,
                TIME_NOW,
            ]);
            $triggerStatement->execute([
                1,
                $this->parameters['authorID'] ? 0 : 1,
                $notificationData['object']->notificationID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        $notificationList = new UserNotificationList();
        $notificationList->setObjectIDs($notificationIDs);
        $notificationList->readObjects();
        $updatedNotifications = $notificationList->getObjects();

        $notifications = \array_map(static function ($notificationData) use ($updatedNotifications) {
            $notificationData['object'] = $updatedNotifications[$notificationData['object']->notificationID];

            return $notificationData;
        }, $notifications);

        return $notifications;
    }

    /**
     * Validates the 'getOutstandingNotifications' action.
     */
    public function validateGetOutstandingNotifications()
    {
        // does nothing
    }

    /**
     * Loads user notifications.
     *
     * @return  mixed[]
     */
    public function getOutstandingNotifications()
    {
        $notifications = UserNotificationHandler::getInstance()->getMixedNotifications();
        $data = [];
        foreach ($notifications['notifications'] as $notification) {
            /** @var IUserNotificationEvent $event */
            $event = $notification['event'];
            $notificationID = $notification['notificationID'];

            $link = '';
            if ($event->isConfirmed()) {
                $link = $event->getLink();
            } else {
                $link = LinkHandler::getInstance()->getLink('NotificationConfirm', ['id' => $notificationID]);
            }

            $itemData = [
                'isConfirmed' => $event->isConfirmed(),
                'link' => $link,
                'objectId' => $notificationID,
                'text' => $event->getMessage(),
                'time' => $notification['time'],
            ];

            if (count($event->getAuthors()) > 1) {
                $itemData['image'] = ['className' => 'fa-users'];
            } else {
                $itemData['image'] = ['url' => $event->getAuthor()->getAvatar()->getURL()];
            }

            $data[] = $itemData;
        }

        return $data;
    }

    /**
     * Validates parameters to mark a notification as confirmed.
     */
    public function validateMarkAsConfirmed()
    {
        $this->notificationEditor = $this->getSingleObject();
        if ($this->notificationEditor->userID != WCF::getUser()->userID) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Marks a notification as confirmed.
     *
     * @return  array
     */
    public function markAsConfirmed()
    {
        //UserNotificationHandler::getInstance()->markAsConfirmedByID($this->notificationEditor->notificationID);

        return [
            'markAsRead' => $this->notificationEditor->notificationID,
            'totalCount' => UserNotificationHandler::getInstance()->getNotificationCount(true),
        ];
    }

    /**
     * Validates parameters to mark all notifications of current user as confirmed.
     */
    public function validateMarkAllAsConfirmed()
    {
        // does nothing
    }

    /**
     * Marks all notifications of current user as confirmed.
     *
     * @return  bool[]
     */
    public function markAllAsConfirmed()
    {
        // remove notifications for this user
        $sql = "UPDATE  wcf" . WCF_N . "_user_notification
                SET     confirmTime = ?
                WHERE   userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            TIME_NOW,
            WCF::getUser()->userID,
        ]);

        // delete notification_to_user assignments (mimic legacy notification system)
        $sql = "DELETE FROM wcf" . WCF_N . "_user_notification_to_user
                WHERE       userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID]);

        // reset notification count
        UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'userNotificationCount');

        return [
            'markAllAsRead' => true,
        ];
    }
}
