<?php

namespace wcf\data\clipboard\action;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of clipboard actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<ClipboardAction>
 */
class ClipboardActionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ClipboardAction::class;
}
