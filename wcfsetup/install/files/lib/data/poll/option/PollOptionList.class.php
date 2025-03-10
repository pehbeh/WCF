<?php

namespace wcf\data\poll\option;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of poll options.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<PollOption>
 */
class PollOptionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PollOption::class;
}
