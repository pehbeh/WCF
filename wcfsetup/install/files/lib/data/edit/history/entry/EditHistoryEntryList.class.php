<?php

namespace wcf\data\edit\history\entry;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of edit history entries.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<EditHistoryEntry>
 */
class EditHistoryEntryList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = EditHistoryEntry::class;
}
