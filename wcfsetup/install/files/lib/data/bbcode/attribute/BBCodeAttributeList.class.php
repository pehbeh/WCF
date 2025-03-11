<?php

namespace wcf\data\bbcode\attribute;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of bbcode attribute.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BBCodeAttribute>
 */
class BBCodeAttributeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BBCodeAttribute::class;
}
