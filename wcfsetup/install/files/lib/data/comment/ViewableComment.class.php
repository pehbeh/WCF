<?php

namespace wcf\data\comment;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;

/**
 * Represents a viewable comment.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin   Comment
 * @extends DatabaseObjectDecorator<Comment>
 */
class ViewableComment extends DatabaseObjectDecorator
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Comment::class;

    /**
     * user profile of the comment author
     * @var UserProfile
     */
    protected $userProfile;

    /**
     * Returns the user profile object.
     *
     * @return  UserProfile
     */
    public function getUserProfile()
    {
        if ($this->userProfile === null) {
            if ($this->userID) {
                $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
            } else {
                $this->userProfile = UserProfile::getGuestUserProfile($this->username);
            }
        }

        return $this->userProfile;
    }

    /**
     * Returns a specific comment decorated as comment entry.
     *
     * @param int $commentID
     * @return  ViewableComment
     */
    public static function getComment($commentID)
    {
        $list = new ViewableCommentList();
        $list->setObjectIDs([$commentID]);
        $list->readObjects();

        return $list->getSingleObject();
    }
}
