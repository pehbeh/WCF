<?php

namespace wcf\data\smiley;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of smilies.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Smiley>
 */
class SmileyList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Smiley::class;
}
