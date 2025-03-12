<?php

namespace wcf\data\blacklist\entry;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of blacklist entries.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BlacklistEntry>
 * @since 5.2
 */
class BlacklistEntryList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BlacklistEntry::class;
}
