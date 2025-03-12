<?php

namespace wcf\system\box;

use wcf\data\user\online\UsersOnlineList;
use wcf\system\user\UserProfileHandler;
use wcf\system\WCF;

/**
 * Lists online users the active user is following.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 *
 * @extends AbstractDatabaseObjectListBoxController<UsersOnlineList>
 */
class FollowingsOnlineBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 10;

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        $objectList = new UsersOnlineList();
        $objectList->getConditionBuilder()->add(
            'session.userID IN (?)',
            [UserProfileHandler::getInstance()->getFollowingUsers()]
        );

        return $objectList;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        return WCF::getTPL()->render('wcf', 'boxFollowingsOnline', ['usersOnlineList' => $this->objectList]);
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        if (!MODULE_USERS_ONLINE || !WCF::getSession()->getPermission('user.profile.canViewUsersOnlineList') || empty(UserProfileHandler::getInstance()->getFollowingUsers())) {
            return false;
        }

        return parent::hasContent();
    }
}
