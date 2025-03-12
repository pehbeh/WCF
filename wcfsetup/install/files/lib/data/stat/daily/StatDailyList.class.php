<?php

namespace wcf\data\stat\daily;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of statistic entries.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<StatDaily>
 */
class StatDailyList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = StatDaily::class;
}
