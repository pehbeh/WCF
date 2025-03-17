<?php

namespace wcf\data\object\type\definition;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes object type definition-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<ObjectTypeDefinition, ObjectTypeDefinitionEditor>
 */
class ObjectTypeDefinitionAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ObjectTypeDefinitionEditor::class;
}
