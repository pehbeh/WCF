<?php

namespace wcf\data\bbcode;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of bbcodes.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BBCode>
 */
class BBCodeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BBCode::class;
}
