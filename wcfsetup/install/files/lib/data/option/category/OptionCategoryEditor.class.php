<?php

namespace wcf\data\option\category;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit option categories.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       OptionCategory
 * @extends DatabaseObjectEditor<OptionCategory>
 */
class OptionCategoryEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = OptionCategory::class;
}
