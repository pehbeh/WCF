<?php

namespace wcf\data\package\installation\plugin;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit package installation plugins.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       PackageInstallationPlugin
 * @extends DatabaseObjectEditor<PackageInstallationPlugin>
 */
class PackageInstallationPluginEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PackageInstallationPlugin::class;
}
