<?php

namespace wcf\data\user\group;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of user groups.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserGroup>
 */
class UserGroupList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserGroup::class;
}
