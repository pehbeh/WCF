<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\ACPSessionLogGridView;

/**
 * Shows a list of logged sessions.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<ACPSessionLogGridView>
 */
class ACPSessionLogListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.log.session';

    /**
     * @inheritDoc
     */
    public $templateName = 'acpSessionLogList';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.management.canViewLog'];

    #[\Override]
    protected function createGridView(): ACPSessionLogGridView
    {
        return new ACPSessionLogGridView();
    }
}
