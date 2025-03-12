<?php

namespace wcf\data\style;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of styles.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Style>
 */
class StyleList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Style::class;
}
