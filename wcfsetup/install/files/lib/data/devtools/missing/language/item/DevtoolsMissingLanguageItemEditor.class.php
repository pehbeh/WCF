<?php

namespace wcf\data\devtools\missing\language\item;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit missing language item log entry.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2020 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.3
 *
 * @mixin       DevtoolsMissingLanguageItem
 * @extends DatabaseObjectEditor<DevtoolsMissingLanguageItem>
 */
class DevtoolsMissingLanguageItemEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = DevtoolsMissingLanguageItem::class;
}
