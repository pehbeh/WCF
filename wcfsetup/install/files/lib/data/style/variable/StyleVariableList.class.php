<?php

namespace wcf\data\style\variable;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of style variables.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<StyleVariable>
 */
class StyleVariableList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = StyleVariable::class;
}
