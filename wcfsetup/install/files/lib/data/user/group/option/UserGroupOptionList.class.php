<?php

namespace wcf\data\user\group\option;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of user group options.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserGroupOption>
 */
class UserGroupOptionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserGroupOption::class;
}
