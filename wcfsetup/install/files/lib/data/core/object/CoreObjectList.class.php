<?php

namespace wcf\data\core\object;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of core objects.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<CoreObject>
 */
class CoreObjectList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = CoreObject::class;
}
