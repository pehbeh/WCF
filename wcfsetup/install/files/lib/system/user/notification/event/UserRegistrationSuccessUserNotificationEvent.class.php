<?php

namespace wcf\system\user\notification\event;

use wcf\data\user\UserProfile;
use wcf\page\UserPage;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\object\UserRegistrationUserNotificationObject;

/**
 * Notification event for users that completed the registration process.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 *
 * @method  UserRegistrationUserNotificationObject  getUserNotificationObject()
 */
class UserRegistrationSuccessUserNotificationEvent extends AbstractUserNotificationEvent implements
    ITestableUserNotificationEvent
{
    use TTestableUserNotificationEvent;

    #[\Override]
    public static function getTestObjects(UserProfile $recipient, UserProfile $author)
    {
        return [new UserRegistrationUserNotificationObject($author->getDecoratedObject())];
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->getLanguage()->get('wcf.user.notification.registrationSuccess.title');
    }

    #[\Override]
    public function getMessage()
    {
        return $this->getLanguage()->getDynamicVariable(
            'wcf.user.notification.registrationSuccess.message',
            [
                'author' => $this->author,
                'notification' => $this->notification,
                'username' => $this->getUserNotificationObject()->username,
                'userNotificationObject' => $this->getUserNotificationObject(),
            ]
        );
    }

    #[\Override]
    public function getEmailMessage($notificationType = 'instant')
    {
        return [
            'template' => 'email_notification_userRegistrationSuccess',
            'application' => 'wcf',
            'variables' => [
                'notification' => $this->notification,
                'username' => $this->getUserNotificationObject()->username,
                'userNotificationObject' => $this->getUserNotificationObject(),
            ],
        ];
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getControllerLink(UserPage::class, [
            'object' => $this->getUserNotificationObject(),
        ]);
    }

    #[\Override]
    public function getEventHash()
    {
        return \sha1($this->eventID);
    }
}
