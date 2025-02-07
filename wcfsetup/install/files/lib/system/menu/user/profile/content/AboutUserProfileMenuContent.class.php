<?php

namespace wcf\system\menu\user\profile\content;

use wcf\data\user\User;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\option\user\UserOptionHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles user profile information content.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class AboutUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent
{
    /**
     * user option handler object
     * @var UserOptionHandler
     */
    public $optionHandler;

    /**
     * @inheritDoc
     */
    public function getContent($userID)
    {
        if ($this->optionHandler === null) {
            $this->optionHandler = new UserOptionHandler(false, '', 'profile');
            $this->optionHandler->enableEditMode(false);
            $this->optionHandler->showEmptyOptions(false);
        }

        $user = new User($userID);
        $this->optionHandler->setUser($user);

        return WCF::getTPL()->render('wcf', 'userProfileAbout', [
            'options' => $this->optionHandler->getOptionTree(),
            'userID' => $user->userID,
            'user' => UserProfileRuntimeCache::getInstance()->getObject($user->userID),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isVisible($userID)
    {
        return true;
    }
}
