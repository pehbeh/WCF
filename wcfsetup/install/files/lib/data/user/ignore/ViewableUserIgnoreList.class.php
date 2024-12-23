<?php

namespace wcf\data\user\ignore;

use wcf\data\user\TUserAvatarObjectList;
use wcf\data\user\User;
use wcf\data\user\UserProfile;

/**
 * Represents a list of ignored users.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ViewableUserIgnoreList extends UserIgnoreList
{
    use TUserAvatarObjectList;

    /**
     * @inheritDoc
     */
    public $className = UserIgnore::class;

    /**
     * @inheritDoc
     */
    public $decoratorClassName = UserProfile::class;

    /**
     * @inheritDoc
     */
    public $objectClassName = User::class;

    /**
     * @inheritDoc
     */
    public $useQualifiedShorthand = false;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects .= "user_ignore.ignoreID";
        $this->sqlSelects .= ", user_option_value.*";

        $this->sqlJoins .= "
            LEFT JOIN   wcf1_user user_table
            ON          user_table.userID = user_ignore.ignoreUserID
            LEFT JOIN   wcf1_user_option_value user_option_value
            ON          user_option_value.userID = user_table.userID";

        $this->sqlSelects .= ", user_table.*";
    }

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        $this->cacheAvatarFiles();
    }
}
