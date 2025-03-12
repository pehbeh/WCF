<?php

namespace wcf\data\package\installation\plugin;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of package installation plugins.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<PackageInstallationPlugin>
 */
class PackageInstallationPluginList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PackageInstallationPlugin::class;
}
