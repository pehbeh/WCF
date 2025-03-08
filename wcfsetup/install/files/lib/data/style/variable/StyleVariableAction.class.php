<?php

namespace wcf\data\style\variable;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes style variable-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<StyleVariable, StyleVariableEditor>
 */
class StyleVariableAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = StyleVariableEditor::class;
}
