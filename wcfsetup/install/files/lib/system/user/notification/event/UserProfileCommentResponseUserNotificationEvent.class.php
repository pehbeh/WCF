<?php

namespace wcf\system\user\notification\event;

use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\CommentRuntimeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\comment\CommentHandler;
use wcf\system\email\Email;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;

/**
 * User notification event for profile comment responses.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  CommentResponseUserNotificationObject   getUserNotificationObject()
 */
class UserProfileCommentResponseUserNotificationEvent extends AbstractCommentResponseUserNotificationEvent implements
    ITestableUserNotificationEvent
{
    use TTestableCommentResponseUserNotificationEvent;

    /**
     * @inheritDoc
     */
    protected function prepare()
    {
        CommentRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->commentID);
        UserProfileRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        $owner = UserProfileRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);

        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable('wcf.user.notification.commentResponse.message.stacked', [
                'authors' => \array_values($authors),
                'commentID' => $this->getUserNotificationObject()->commentID,
                'count' => $count,
                'others' => $count - 1,
                'owner' => $owner,
                'guestTimesTriggered' => $this->notification->guestTimesTriggered,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('wcf.user.notification.commentResponse.message', [
            'author' => $this->author,
            'commentID' => $this->getUserNotificationObject()->commentID,
            'owner' => $owner,
            'responseID' => $this->getUserNotificationObject()->responseID,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        $comment = CommentRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->commentID);
        $owner = UserProfileRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);

        $messageID = '<com.woltlab.wcf.user.profileComment.notification/' . $comment->commentID . '@' . Email::getHost() . '>';

        return [
            'template' => 'email_notification_commentResponse',
            'application' => 'wcf',
            'in-reply-to' => [$messageID],
            'references' => [$messageID],
            'variables' => [
                'commentID' => $this->getUserNotificationObject()->commentID,
                'owner' => $owner,
                'responseID' => $this->getUserNotificationObject()->responseID,
                'languageVariablePrefix' => 'wcf.user.notification.commentResponse',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return UserProfileRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#wall/comment' . $this->getUserNotificationObject()->commentID;
    }

    /**
     * @inheritDoc
     */
    protected function getTypeName(): string
    {
        return $this->getLanguage()->get('wcf.user.profile.menu.wall');
    }

    /**
     * @inheritDoc
     */
    protected function getObjectTitle(): string
    {
        return UserProfileRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->username;
    }

    /**
     * @inheritDoc
     * @return array{objectID: int, objectTypeID: ?int}
     * @since   3.1
     */
    protected static function getTestCommentObjectData(UserProfile $recipient, UserProfile $author)
    {
        return [
            'objectID' => $author->userID,
            'objectTypeID' => CommentHandler::getInstance()->getObjectTypeID('com.woltlab.wcf.user.profileComment'),
        ];
    }
}
