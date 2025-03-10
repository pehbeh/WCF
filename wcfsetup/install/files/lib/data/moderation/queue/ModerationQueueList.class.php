<?php

namespace wcf\data\moderation\queue;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of moderation queue entries.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template-covariant TDatabaseObject of DatabaseObject|DatabaseObjectDecorator<DatabaseObject> = ModerationQueue
 * @extends DatabaseObjectList<TDatabaseObject>
 */
class ModerationQueueList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ModerationQueue::class;
}
