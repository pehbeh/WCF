<?php

namespace wcf\data\package\update\version;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes package update version-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<PackageUpdateVersion, PackageUpdateVersionEditor>
 */
class PackageUpdateVersionAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = PackageUpdateVersionEditor::class;
}
