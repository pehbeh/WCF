<?php

namespace wcf\data\core\object;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit core objects.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       CoreObject
 * @extends DatabaseObjectEditor<CoreObject>
 */
class CoreObjectEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = CoreObject::class;
}
