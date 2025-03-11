<?php

namespace wcf\data\cronjob;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of cronjobs.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Cronjob>
 */
class CronjobList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Cronjob::class;
}
