<?php

namespace wcf\acp\page;

use wcf\data\package\update\server\PackageUpdateServer;
use wcf\page\AbstractPage;
use wcf\system\package\PackageUpdateDispatcher;
use wcf\system\WCF;

/**
 * Shows the package update confirmation form.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageUpdatePage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.package';

    /**
     * list of available updates
     * @var mixed[]
     */
    public $availableUpdates = [];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.configuration.package.canUpdatePackage'];

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        $this->availableUpdates = PackageUpdateDispatcher::getInstance()->getAvailableUpdates(true, true);

        // Reduce the versions into a single value.
        foreach ($this->availableUpdates as &$update) {
            $latestVersion = \reset($update['versions']);
            $update['newVersion'] = $latestVersion;
        }
        unset($update);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        $woltlabUpdateServer = \array_filter(PackageUpdateServer::getActiveUpdateServers(), static function (PackageUpdateServer $updateServer) {
            return $updateServer->isWoltLabUpdateServer();
        });

        WCF::getTPL()->assign([
            'availableUpdates' => $this->availableUpdates,
            'items' => \count($this->availableUpdates),
            'upgradeOverrideEnabled' => PackageUpdateServer::isUpgradeOverrideEnabled(),
            'woltlabUpdateServer' => \reset($woltlabUpdateServer),
        ]);
    }
}
