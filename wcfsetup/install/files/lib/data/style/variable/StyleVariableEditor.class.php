<?php

namespace wcf\data\style\variable;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to create, edit and delete a style variable.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       StyleVariable
 * @extends DatabaseObjectEditor<StyleVariable>
 */
class StyleVariableEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = StyleVariable::class;
}
