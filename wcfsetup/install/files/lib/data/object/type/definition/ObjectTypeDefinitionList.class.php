<?php

namespace wcf\data\object\type\definition;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of object type definitions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<ObjectTypeDefinition>
 */
class ObjectTypeDefinitionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ObjectTypeDefinition::class;
}
