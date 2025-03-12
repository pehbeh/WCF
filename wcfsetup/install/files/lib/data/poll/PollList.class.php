<?php

namespace wcf\data\poll;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of polls.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Poll>
 */
class PollList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Poll::class;
}
