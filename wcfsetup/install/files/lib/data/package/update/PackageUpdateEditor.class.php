<?php

namespace wcf\data\package\update;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit package updates.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       PackageUpdate
 * @extends DatabaseObjectEditor<PackageUpdate>
 */
class PackageUpdateEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PackageUpdate::class;
}
