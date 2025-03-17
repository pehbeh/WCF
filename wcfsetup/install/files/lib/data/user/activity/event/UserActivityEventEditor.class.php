<?php

namespace wcf\data\user\activity\event;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit user activity events.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       UserActivityEvent
 * @extends DatabaseObjectEditor<UserActivityEvent>
 */
class UserActivityEventEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserActivityEvent::class;
}
