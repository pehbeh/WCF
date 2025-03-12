<?php

namespace wcf\data\blacklist\status;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of blacklist status.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BlacklistStatus>
 * @since 5.2
 */
class BlacklistStatusList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BlacklistStatus::class;
}
