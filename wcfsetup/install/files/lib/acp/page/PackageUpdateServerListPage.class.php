<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\PackageUpdateServerGridView;

/**
 * Shows information about available update package servers.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<PackageUpdateServerGridView>
 */
class PackageUpdateServerListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.package.server.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.configuration.package.canEditServer'];

    #[\Override]
    protected function createGridView(): PackageUpdateServerGridView
    {
        return new PackageUpdateServerGridView();
    }
}
