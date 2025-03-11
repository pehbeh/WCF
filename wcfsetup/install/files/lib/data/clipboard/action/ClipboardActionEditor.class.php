<?php

namespace wcf\data\clipboard\action;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit clipboard actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       ClipboardAction
 * @extends DatabaseObjectEditor<ClipboardAction>
 */
class ClipboardActionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ClipboardAction::class;
}
