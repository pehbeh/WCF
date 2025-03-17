<?php

namespace wcf\data\object\type;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes object type-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<ObjectType, ObjectTypeEditor>
 */
class ObjectTypeAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ObjectTypeEditor::class;
}
