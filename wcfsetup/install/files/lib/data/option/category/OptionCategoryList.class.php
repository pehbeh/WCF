<?php

namespace wcf\data\option\category;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of option categories.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<OptionCategory>
 */
class OptionCategoryList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = OptionCategory::class;
}
