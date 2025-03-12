<?php

namespace wcf\data\user\group\option\category;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of user group option categories.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserGroupOptionCategory>
 */
class UserGroupOptionCategoryList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserGroupOptionCategory::class;
}
