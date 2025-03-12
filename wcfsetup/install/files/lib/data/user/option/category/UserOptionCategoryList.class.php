<?php

namespace wcf\data\user\option\category;

use wcf\data\DatabaseObjectList;

/**
 * Represents an list of user option categories.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserOptionCategory>
 */
class UserOptionCategoryList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserOptionCategory::class;
}
