<?php

namespace wcf\data\user\object\watch;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of watched objects.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserObjectWatch>
 */
class UserObjectWatchList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserObjectWatch::class;
}
