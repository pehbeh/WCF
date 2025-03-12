<?php

namespace wcf\data\cronjob\log;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of cronjob log entries.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<CronjobLog>
 */
class CronjobLogList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = CronjobLog::class;
}
