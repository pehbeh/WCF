<?php

namespace wcf\data\user;

/**
 * Represents a list of user profiles.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  UserProfile     current()
 * @method  UserProfile[]       getObjects()
 * @method  UserProfile|null    getSingleObject()
 * @method  UserProfile|null    search($objectID)
 * @property    UserProfile[] $objects
 */
class UserProfileList extends UserList
{
    use TUserAvatarObjectList;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'user_table.username';

    /**
     * @inheritDoc
     */
    public $decoratorClassName = UserProfile::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }

        // get current location
        $this->sqlSelects .= "session.pageID, session.pageObjectID, session.lastActivityTime AS sessionLastActivityTime";
        $this->sqlJoins .= "
            LEFT JOIN   wcf1_session session
            ON          session.userID = user_table.userID";
    }

    /**
     * @inheritDoc
     */
    public function readObjects()
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();

        $this->cacheAvatarFiles();
    }
}
