<?php

namespace wcf\data\user\trophy;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides database related trophy actions.
 *
 * @author  Joshua Ruesweg
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.1
 * @mixin   UserTrophy
 * @extends DatabaseObjectEditor<UserTrophy>
 */
class UserTrophyEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserTrophy::class;
}
