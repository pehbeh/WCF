<?php

namespace wcf\data\user\group\option\category;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit usergroup option categories.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       UserGroupOptionCategory
 * @extends DatabaseObjectEditor<UserGroupOptionCategory>
 */
class UserGroupOptionCategoryEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserGroupOptionCategory::class;
}
