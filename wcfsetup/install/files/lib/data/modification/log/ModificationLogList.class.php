<?php

namespace wcf\data\modification\log;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of modification logs.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<ModificationLog>
 */
class ModificationLogList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ModificationLog::class;
}
